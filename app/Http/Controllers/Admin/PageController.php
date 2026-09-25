<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Media;
use App\Models\Page;
use App\Support\Blocks\BlockRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => Page::orderByDesc('is_system')->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return $this->editor(new Page(['status' => 'active', 'title' => ['ar' => '', 'en' => ''], 'blocks' => []]));
    }

    public function edit(Page $page): View
    {
        return $this->editor($page);
    }

    private function editor(Page $page): View
    {
        return view('admin.builder.editor', [
            'mode' => 'page',
            'record' => $page,
            'config' => [
                'mode' => 'page',
                'id' => $page->id,
                'system' => (bool) $page->is_system,
                'saveUrl' => $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store'),
                'method' => $page->exists ? 'PUT' : 'POST',
                'liveUrl' => $page->exists ? $page->url() : null,
                'backUrl' => route('admin.pages.index'),
                'data' => [
                    'title' => $page->getTranslations('title') + ['ar' => '', 'en' => ''],
                    'slug' => $page->slug,
                    'status' => $page->status ?? 'active',
                    'in_menu' => (bool) $page->in_menu,
                    'seo' => ($page->seo ?? []) + ['title' => ['ar' => '', 'en' => ''], 'description' => ['ar' => '', 'en' => '']],
                    'blocks' => $page->blocks ?? [],
                ],
                'media' => Media::with('poster')->whereIn('id', BlockRegistry::mediaIds($page->blocks ?? []))->get()
                    ->mapWithKeys(fn ($m) => [$m->id => $m->toPicker()]),
                'categories' => Category::ordered()->get()->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'subs' => []]),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $page = new Page(['is_system' => false]);
        $this->fillAndSave($request, $page);

        return response()->json([
            'message' => __('Page created.'),
            'redirect' => route('admin.pages.edit', $page),
        ]);
    }

    public function update(Request $request, Page $page): JsonResponse
    {
        $this->fillAndSave($request, $page);

        return response()->json(['message' => __('Saved.'), 'liveUrl' => $page->url(), 'slug' => $page->slug]);
    }

    private function fillAndSave(Request $request, Page $page): void
    {
        $data = $request->validate([
            'title' => ['required', 'array'],
            'title.ar' => ['nullable', 'string', 'max:200', 'required_without:title.en'],
            'title.en' => ['nullable', 'string', 'max:200', 'required_without:title.ar'],
            'slug' => $page->is_system ? ['nullable'] : ['nullable', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::notIn([Page::HOME, Page::ABOUT]), Rule::unique('pages', 'slug')->ignore($page->id)],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'in_menu' => ['boolean'],
            'seo' => ['nullable', 'array'],
            'seo.title.*' => ['nullable', 'string', 'max:160'],
            'seo.description.*' => ['nullable', 'string', 'max:300'],
            'blocks' => ['present', 'array'],
        ]);

        $data['blocks'] = BlockRegistry::sanitize($data['blocks']);
        $data['title'] = array_map(fn ($v) => (string) $v, $data['title']);

        if ($page->is_system) {
            unset($data['slug'], $data['in_menu']);
        } else {
            $data['slug'] = $data['slug'] ?: $this->uniqueSlug($data['title']['en'] ?: $data['title']['ar']);
            $data['in_menu'] = $request->boolean('in_menu');
        }

        $page->fill($data)->save();
        site_changed();
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'page';
        $slug = $base;
        $i = 2;
        while (Page::where('slug', $slug)->exists() || in_array($slug, [Page::HOME, Page::ABOUT], true)) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function destroy(Page $page): JsonResponse
    {
        abort_if($page->is_system, 403, __('System pages cannot be deleted.'));

        $page->delete();
        site_changed();

        return response()->json(['message' => __('Page deleted.')]);
    }
}
