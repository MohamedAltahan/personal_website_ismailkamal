<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listing($request);
    }

    public function category(Request $request, Category $category): View
    {
        abort_unless($category->status === 'active', 404);

        return $this->listing($request, $category);
    }

    private function listing(Request $request, ?Category $category = null): View
    {
        $perPage = max(3, (int) setting('appearance.projects_per_page', 12));

        $projects = Project::published()
            ->with(['cover', 'hover', 'category'])
            ->when($category, fn ($q) => $q->where('category_id', $category->id))
            ->when($category && $request->query('sub'), fn ($q) => $q->where('sub_category_id', $request->query('sub')))
            ->ordered()
            ->paginate($perPage)
            ->withQueryString();

        $view = $request->header('X-Fragment') === '1' ? 'site.work.fragment' : 'site.work.index';

        return view($view, [
            'projects' => $projects,
            'category' => $category?->load(['subCategories' => fn ($q) => $q->active()]),
            'total' => $projects->total(),
            'offset' => ($projects->currentPage() - 1) * $perPage,
        ]);
    }

    public function show(Project $project): View
    {
        abort_unless($project->isPublished() || auth()->check(), 404);

        $project->load(['cover', 'category', 'subCategory']);
        $project->increment('views');

        $ids = Project::published()->ordered()->pluck('id')->values();
        $index = $ids->search($project->id);
        $nextId = $index === false || $ids->count() < 2 ? null : $ids[($index + 1) % $ids->count()];

        return view('site.work.show', [
            'project' => $project,
            'media' => $project->blockMedia(),
            'next' => $nextId ? Project::with(['cover', 'category'])->find($nextId) : null,
        ]);
    }
}
