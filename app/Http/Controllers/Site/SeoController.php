<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Project;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = [];
        $add = function (callable $url, $updated = null) use (&$urls) {
            $urls[] = [
                'alternates' => collect(array_keys(locales()))->mapWithKeys(fn ($l) => [$l => $url($l)])->all(),
                'updated' => $updated?->toAtomString(),
            ];
        };

        $add(fn ($l) => lroute('home', [], $l));
        $add(fn ($l) => lroute('work.index', [], $l));
        $add(fn ($l) => lroute('about', [], $l));
        $add(fn ($l) => lroute('contact', [], $l));

        foreach (Category::active()->get() as $category) {
            $add(fn ($l) => lroute('work.category', $category->slug, $l), $category->updated_at);
        }
        foreach (Project::published()->get() as $project) {
            $add(fn ($l) => $project->url($l), $project->updated_at);
        }
        foreach (Page::active()->where('is_system', false)->get() as $page) {
            $add(fn ($l) => $page->url($l), $page->updated_at);
        }

        return response()->view('site.sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $body = setting('seo.indexable')
            ? "User-agent: *\nDisallow: /admin\n\nSitemap: ".route('sitemap')."\n"
            : "User-agent: *\nDisallow: /\n";

        return response($body)->header('Content-Type', 'text/plain');
    }
}
