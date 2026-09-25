<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(private readonly MediaService $media) {}

    public function index(Request $request): View
    {
        $query = $this->filtered($request);

        $items = $query->paginate(48)->withQueryString();

        $usage = DB::table('mediables')->whereIn('media_id', $items->pluck('id'))
            ->selectRaw('media_id, COUNT(*) as c')->groupBy('media_id')->pluck('c', 'media_id');
        $covers = DB::table('designs')->whereIn('cover_media_id', $items->pluck('id'))
            ->selectRaw('cover_media_id, COUNT(*) as c')->groupBy('cover_media_id')->pluck('c', 'cover_media_id');

        return view('admin.media.index', [
            'items' => $items,
            'usage' => $usage,
            'covers' => $covers,
            'filters' => $request->only(['q', 'type', 'folder', 'unused']),
            'totals' => [
                'count' => Media::count(),
                'bytes' => (int) Media::sum('size'),
                'images' => Media::where('type', 'image')->count(),
                'videos' => Media::whereIn('type', ['video', 'embed'])->count(),
            ],
        ]);
    }

    /** JSON list for the media picker. */
    public function list(Request $request): JsonResponse
    {
        $page = $this->filtered($request)->paginate(36);

        return response()->json([
            'data' => $page->getCollection()->map->toPicker()->values(),
            'next_page' => $page->hasMorePages() ? $page->currentPage() + 1 : null,
        ]);
    }

    private function filtered(Request $request)
    {
        $accept = array_filter(explode(',', (string) $request->query('accept')));

        return Media::with('poster')
            // Posters are managed through their video; hide them from the grid.
            ->whereNotIn('id', DB::table('media')->whereNotNull('poster_id')->select('poster_id'))
            ->when($accept, fn ($q) => $q->whereIn('type', $accept))
            ->when($request->query('type'), fn ($q, $type) => $q->where('type', $type))
            ->when($request->query('folder'), fn ($q, $folder) => $q->where('folder', $folder))
            ->when($request->query('q'), fn ($q, $term) => $q->where('name', 'like', '%'.$term.'%'))
            ->when($request->boolean('unused'), fn ($q) => $q
                ->whereNotIn('id', DB::table('mediables')->select('media_id'))
                ->whereNotIn('id', DB::table('designs')->whereNotNull('cover_media_id')->select('cover_media_id'))
                ->whereNotIn('id', DB::table('designs')->whereNotNull('hover_media_id')->select('hover_media_id')))
            ->latest('id');
    }

    /**
     * Chunked upload endpoint. Each request carries one chunk; the last one assembles
     * the file and creates the media (plus an optional browser-generated video poster).
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'upload_id' => ['required', 'alpha_num', 'max:40'],
            'chunk_index' => ['required', 'integer', 'min:0'],
            'total_chunks' => ['required', 'integer', 'min:1', 'max:5000'],
            'file_name' => ['required', 'string', 'max:255'],
            'chunk' => ['required', 'file'],
            'folder' => ['nullable', 'string', 'max:64'],
            'poster' => ['nullable', 'image', 'max:10240'],
            'width' => ['nullable', 'integer'],
            'height' => ['nullable', 'integer'],
            'duration' => ['nullable', 'integer'],
        ]);

        $dir = storage_path('app/private/chunks');
        File::ensureDirectoryExists($dir);
        $partial = $dir.'/'.$request->input('upload_id').'.part';

        $index = (int) $request->input('chunk_index');
        $total = (int) $request->input('total_chunks');

        if ($index === 0 && is_file($partial)) {
            unlink($partial);
        }

        $out = fopen($partial, 'ab');
        $in = fopen($request->file('chunk')->getRealPath(), 'rb');
        stream_copy_to_stream($in, $out);
        fclose($in);
        fclose($out);

        $maxBytes = (int) setting('media.max_upload_mb', 512) * 1024 * 1024;
        if (filesize($partial) > $maxBytes) {
            @unlink($partial);
            abort(422, __('The file is larger than :size MB.', ['size' => setting('media.max_upload_mb')]));
        }

        if ($index < $total - 1) {
            return response()->json(['received' => $index + 1]);
        }

        try {
            $attributes = [];
            if ($request->filled('width')) {
                $attributes += ['width' => $request->integer('width'), 'height' => $request->integer('height'),
                    'meta' => ['duration' => $request->integer('duration')]];
            }

            $media = $this->media->store($partial, $request->input('file_name'), $request->input('folder'), $attributes);

            if ($media->isVideo() && $request->hasFile('poster')) {
                $poster = $this->media->store($request->file('poster'), 'poster-'.pathinfo($media->name, PATHINFO_FILENAME).'.jpg', 'posters');
                $media->update(['poster_id' => $poster->id]);
            }
        } finally {
            @unlink($partial);
        }

        return response()->json(['media' => $media->fresh('poster')->toPicker()]);
    }

    public function embed(Request $request): JsonResponse
    {
        $request->validate(['url' => ['required', 'url', 'max:500']]);

        return response()->json(['media' => $this->media->embed($request->input('url'))->toPicker()]);
    }

    public function update(Request $request, Media $media): JsonResponse
    {
        $data = $request->validate([
            'alt' => ['nullable', 'array'],
            'alt.*' => ['nullable', 'string', 'max:250'],
            'caption' => ['nullable', 'array'],
            'caption.*' => ['nullable', 'string', 'max:500'],
            'name' => ['nullable', 'string', 'max:180'],
        ]);

        $media->fill(array_filter($data, fn ($v) => $v !== null))->save();

        return response()->json(['media' => $media->toPicker(), 'message' => __('Saved.')]);
    }

    /** Replace a video's poster with an uploaded image or an existing library image. */
    public function poster(Request $request, Media $media): JsonResponse
    {
        abort_unless($media->isVideo(), 422);

        $request->validate(['poster_id' => ['nullable', 'exists:media,id'], 'file' => ['nullable', 'image', 'max:20480']]);

        $posterId = $request->hasFile('file')
            ? $this->media->store($request->file('file'), null, 'posters')->id
            : $request->input('poster_id');

        $media->update(['poster_id' => $posterId]);

        return response()->json(['media' => $media->fresh('poster')->toPicker(), 'message' => __('Poster updated.')]);
    }

    public function destroy(Media $media): JsonResponse
    {
        if ($used = $media->usageCount()) {
            return response()->json(['message' => __('This file is used in :n place(s). Remove it from there first.', ['n' => $used])], 422);
        }

        if ($media->poster && $media->poster->usageCount() === 0) {
            $this->media->delete($media->poster);
        }

        $this->media->delete($media);

        return response()->json(['message' => __('Deleted.')]);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']])['ids'];
        $deleted = 0;
        $skipped = 0;

        foreach (Media::whereIn('id', $ids)->get() as $media) {
            if ($media->usageCount()) {
                $skipped++;
                continue;
            }
            $this->media->delete($media);
            $deleted++;
        }

        return response()->json([
            'deleted' => $deleted,
            'message' => $skipped
                ? __(':d deleted, :s skipped because they are in use.', ['d' => $deleted, 's' => $skipped])
                : __(':d deleted.', ['d' => $deleted]),
        ]);
    }

    /**
     * Rebuild image variants in small batches so it works without a queue worker:
     * the settings page calls this repeatedly with the next offset and shows progress.
     */
    public function regenerate(Request $request): JsonResponse
    {
        $after = $request->integer('after');
        $batch = Media::images()->where('id', '>', $after)->orderBy('id')->take(4)->get();
        $failed = [];

        foreach ($batch as $media) {
            try {
                $this->media->generateVariants($media);
            } catch (\Throwable $e) {
                report($e);
                $failed[] = $media->name;
            }
        }

        return response()->json([
            'total' => Media::images()->count(),
            'done' => Media::images()->where('id', '<=', $batch->last()?->id ?? PHP_INT_MAX)->count(),
            'next' => $batch->count() ? $batch->last()->id : null,
            'failed' => $failed,
        ]);
    }
}
