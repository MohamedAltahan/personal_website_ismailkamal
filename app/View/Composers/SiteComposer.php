<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Social;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/** Shares navigation data with every public view (cached, flushed on admin saves). */
class SiteComposer
{
    private static ?array $shared = null;

    public function compose(View $view): void
    {
        static::$shared ??= Cache::rememberForever('site_nav', fn () => [
            'navCategories' => Category::active()->ordered()->whereHas('projects', fn ($q) => $q->published())->get(),
            'navPages' => Page::active()->where('in_menu', true)->orderBy('sort_order')->get(),
            'socials' => Social::active()->get(),
        ]);

        $view->with(static::$shared);
    }

    public static function flush(): void
    {
        Cache::forget('site_nav');
        static::$shared = null;
    }
}
