<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return $this->render(Page::findSlug(Page::HOME));
    }

    public function about(): View
    {
        return $this->render(Page::findSlug(Page::ABOUT));
    }

    public function show(Page $page): View
    {
        abort_if($page->is_system, 404);

        return $this->render($page);
    }

    private function render(?Page $page): View
    {
        abort_if(! $page || ($page->status !== 'active' && ! auth()->check()), 404);

        return view('site.page', [
            'page' => $page,
            'media' => $page->blockMedia(),
        ]);
    }
}
