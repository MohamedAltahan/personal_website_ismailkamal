<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Media;
use App\Models\Project;
use App\Support\Blocks\BlockRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'category', 'status']);

        // Grid / list preference: from the query string, remembered in a cookie.
        $view = in_array($request->query('view'), ['grid', 'list'], true)
            ? $request->query('view')
            : ($request->cookie('projects_view') === 'list' ? 'list' : 'grid');
        cookie()->queue('projects_view', $view, 60 * 24 * 365);

        $projects = Project::with(['cover', 'category'])
            ->when($filters['q'] ?? null, fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('title', 'like', '%'.$term.'%')->orWhere('client', 'like', '%'.$term.'%')))
            ->when($filters['category'] ?? null, fn ($q, $id) => $q->where('category_id', $id))
            ->when($filters['status'] ?? null, fn ($q, $status) => match ($status) {
                'featured' => $q->where('is_featured', true),
                default => $q->where('status', $status),
            })
            ->ordered()
            ->get();

        return view('admin.projects.index', [
            'projects' => $projects,
            'filters' => $filters,
            'view' => $view,
            'categories' => Category::ordered()->get(),
            'canReorder' => ! array_filter($filters),
            'counts' => [
                'all' => Project::count(),
                'active' => Project::where('status', 'active')->count(),
                'inactive' => Project::where('status', 'inactive')->count(),
                'featured' => Project::where('is_featured', true)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return $this->editor(new Project([
            'status' => 'active',
            'title' => ['ar' => '', 'en' => ''],
            'year' => (string) now()->year,
            'blocks' => [],
        ]));
    }

    public function edit(Project $project): View
    {
        return $this->editor($project);
    }

    private function editor(Project $project): View
    {
        $mediaIds = array_filter(array_merge(
            BlockRegistry::mediaIds($project->blocks ?? []),
            [$project->cover_media_id, $project->hover_media_id],
        ));

        return view('admin.builder.editor', [
            'mode' => 'project',
            'record' => $project,
            'config' => [
                'mode' => 'project',
                'id' => $project->id,
                'saveUrl' => $project->exists ? route('admin.projects.update', $project) : route('admin.projects.store'),
                'method' => $project->exists ? 'PUT' : 'POST',
                'liveUrl' => $project->exists && $project->slug ? $project->url() : null,
                'backUrl' => route('admin.projects.index'),
                'data' => [
                    'title' => $project->getTranslations('title') + ['ar' => '', 'en' => ''],
                    'excerpt' => $project->getTranslations('excerpt') + ['ar' => '', 'en' => ''],
                    'slug' => $project->slug,
                    'category_id' => $project->category_id,
                    'sub_category_id' => $project->sub_category_id,
                    'status' => $project->status ?? 'active',
                    'is_featured' => (bool) $project->is_featured,
                    'client' => $project->client,
                    'year' => $project->year,
                    'tags' => $project->tags ?? [],
                    'cover_media_id' => $project->cover_media_id,
                    'hover_media_id' => $project->hover_media_id,
                    'published_at' => $project->published_at?->format('Y-m-d\TH:i'),
                    'seo' => ($project->seo ?? []) + ['title' => ['ar' => '', 'en' => ''], 'description' => ['ar' => '', 'en' => '']],
                    'blocks' => $project->blocks ?? [],
                ],
                'media' => Media::with('poster')->whereIn('id', $mediaIds)->get()->mapWithKeys(fn ($m) => [$m->id => $m->toPicker()]),
                'categories' => Category::with('subCategories')->ordered()->get()->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'subs' => $c->subCategories->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values(),
                ]),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $project = new Project;
        $this->fillAndSave($request, $project);

        return response()->json([
            'message' => __('Project created.'),
            'redirect' => route('admin.projects.edit', $project),
            'liveUrl' => $project->url(),
        ]);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $this->fillAndSave($request, $project);

        return response()->json(['message' => __('Saved.'), 'liveUrl' => $project->url(), 'slug' => $project->slug]);
    }

    private function fillAndSave(Request $request, Project $project): void
    {
        $data = $request->validate([
            'title' => ['required', 'array'],
            'title.ar' => ['nullable', 'string', 'max:200', 'required_without:title.en'],
            'title.en' => ['nullable', 'string', 'max:200', 'required_without:title.ar'],
            'excerpt' => ['nullable', 'array'],
            'excerpt.*' => ['nullable', 'string', 'max:600'],
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('designs', 'slug')->ignore($project->id)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'exists:sub_categories,id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'is_featured' => ['boolean'],
            'client' => ['nullable', 'string', 'max:150'],
            'year' => ['nullable', 'string', 'max:16'],
            'tags' => ['nullable', 'array', 'max:20'],
            'tags.*' => ['string', 'max:40'],
            'cover_media_id' => ['nullable', 'exists:media,id'],
            'hover_media_id' => ['nullable', 'exists:media,id'],
            'published_at' => ['nullable', 'date'],
            'seo' => ['nullable', 'array'],
            'seo.title.*' => ['nullable', 'string', 'max:160'],
            'seo.description.*' => ['nullable', 'string', 'max:300'],
            'blocks' => ['present', 'array'],
        ]);

        $data['blocks'] = BlockRegistry::sanitize($data['blocks']);
        $data['title'] = array_map(fn ($v) => (string) $v, $data['title']);
        $data['excerpt'] = array_map(fn ($v) => (string) $v, $data['excerpt'] ?? []);
        $data['slug'] = $data['slug'] ?: $this->uniqueSlug($data['title']['en'] ?: $data['title']['ar'], $project->id);
        $data['is_featured'] = $request->boolean('is_featured');

        if (! $project->exists) {
            $data['sort_order'] = (int) Project::min('sort_order') - 1;
        }

        DB::transaction(fn () => $project->fill($data)->save());
        site_changed();
    }

    private function uniqueSlug(string $name, ?int $ignore): string
    {
        $base = Str::slug($name) ?: 'project';
        $slug = $base;
        $i = 2;

        while (Project::where('slug', $slug)->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function destroy(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        $project->delete();
        site_changed();

        $message = __('Project deleted. Its media stay in the library.');

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : redirect()->route('admin.projects.index')->with('success', $message);
    }

    public function toggle(Project $project, string $field): JsonResponse
    {
        $value = $field === 'status'
            ? ($project->status === 'active' ? 'inactive' : 'active')
            : ! $project->is_featured;

        $project->update([$field => $value]);
        site_changed();

        return response()->json([
            'value' => $field === 'status' ? $value === 'active' : $value,
            'message' => __('Updated.'),
        ]);
    }

    public function duplicate(Project $project): RedirectResponse
    {
        $copy = $project->replicate(['slug', 'views']);
        $copy->setTranslations('title', collect($project->getTranslations('title'))->map(fn ($t) => $t.' ('.__('copy').')')->all());
        $copy->slug = $this->uniqueSlug(($project->slug ?? 'project').'-copy', null);
        $copy->status = 'inactive';
        $copy->save();

        return redirect()->route('admin.projects.edit', $copy)->with('success', __('Project duplicated as a hidden draft.'));
    }

    public function reorder(Request $request): JsonResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']])['ids'];

        DB::transaction(function () use ($ids) {
            foreach ($ids as $position => $id) {
                Project::whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });
        site_changed();

        return response()->json(['message' => __('Order saved.')]);
    }
}
