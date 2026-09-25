<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetLocale;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Keeps links to the previous site (and search results) working. */
class LegacyRedirectController extends Controller
{
    public function project(Request $request, int $id): RedirectResponse
    {
        $project = Project::findOrFail($id);

        return redirect()->to($project->url(SetLocale::preferred($request)), 301);
    }

    public function category(Request $request, int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        return redirect()->to(lroute('work.category', $category->slug, SetLocale::preferred($request)), 301);
    }

    public function to(Request $request, string $route): RedirectResponse
    {
        return redirect()->to(lroute($route, [], SetLocale::preferred($request)), 301);
    }
}
