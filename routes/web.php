<?php

use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\LegacyRedirectController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\SeoController;
use App\Http\Controllers\Site\WorkController;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (Request $request) => redirect()->to('/'.SetLocale::preferred($request)))->name('root');

Route::get('sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('robots.txt', [SeoController::class, 'robots'])->name('robots');

// Old URLs from the previous site keep working (301).
Route::get('design-details/{id}', [LegacyRedirectController::class, 'project'])->whereNumber('id');
Route::get('category/{id}', [LegacyRedirectController::class, 'category'])->whereNumber('id');
Route::get('about', [LegacyRedirectController::class, 'to'])->defaults('route', 'about');
Route::get('contact', [LegacyRedirectController::class, 'to'])->defaults('route', 'contact');

Route::prefix('{locale}')
    ->whereIn('locale', array_keys(config('site.locales')))
    ->middleware('locale')
    ->group(function () {
        Route::get('/', [PageController::class, 'home'])->name('home');
        Route::get('work', [WorkController::class, 'index'])->name('work.index');
        Route::get('work/{project:slug}', [WorkController::class, 'show'])->name('work.show');
        Route::get('category/{category:slug}', [WorkController::class, 'category'])->name('work.category');
        Route::get('about', [PageController::class, 'about'])->name('about');
        Route::get('contact', [ContactController::class, 'index'])->name('contact');
        Route::post('contact', [ContactController::class, 'store'])->middleware('throttle:5,10')->name('contact.store');
        Route::get('p/{page:slug}', [PageController::class, 'show'])->name('page');
    });
