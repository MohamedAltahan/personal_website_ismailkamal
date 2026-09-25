<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Media;
use App\Models\Page;
use App\Models\Project;
use App\Support\Blocks\BlockRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

/**
 * Renders unsaved builder content with the real public templates, for the
 * live preview iframe in the editor.
 */
class BuilderController extends Controller
{
    public function preview(Request $request)
    {
        $request->validate([
            'mode' => ['required', 'in:project,page'],
            'locale' => ['required', 'in:'.implode(',', array_keys(locales()))],
            'data' => ['required', 'array'],
        ]);

        $locale = $request->input('locale');
        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        $data = $request->input('data');

        try {
            $blocks = BlockRegistry::sanitize($data['blocks'] ?? []);
        } catch (ValidationException) {
            $blocks = [];
        }

        $media = Media::with('poster')->whereIn('id', array_filter(array_merge(
            BlockRegistry::mediaIds($blocks),
            [$data['cover_media_id'] ?? null],
        )))->get()->keyBy('id');

        if ($request->input('mode') === 'page') {
            $page = new Page([
                'slug' => $data['slug'] ?? 'preview',
                'title' => $data['title'] ?? [],
                'status' => 'active',
            ]);
            $page->blocks = $blocks;

            return view('site.page', ['page' => $page, 'media' => $media, 'preview' => true]);
        }

        $project = new Project([
            'title' => $data['title'] ?? [],
            'excerpt' => $data['excerpt'] ?? [],
            'client' => $data['client'] ?? null,
            'year' => $data['year'] ?? null,
            'tags' => $data['tags'] ?? [],
            'slug' => $data['slug'] ?: 'preview',
        ]);
        $project->blocks = $blocks;
        $project->setRelation('cover', $media[$data['cover_media_id'] ?? 0] ?? null);
        $project->setRelation('category', Category::find($data['category_id'] ?? null));

        return view('site.work.show', [
            'project' => $project,
            'media' => $media,
            'next' => null,
            'preview' => true,
        ]);
    }
}
