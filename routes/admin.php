<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BuilderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SocialController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'create'])->name('login');
        Route::post('login', [AuthController::class, 'store'])->middleware('throttle:6,1');
        Route::get('forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
        Route::post('forgot-password', [PasswordResetController::class, 'email'])->middleware('throttle:4,1')->name('password.email');
        Route::get('reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
        Route::post('reset-password', [PasswordResetController::class, 'update'])->name('password.store');
    });

    Route::post('locale/{locale}', function (string $locale) {
        abort_unless(array_key_exists($locale, locales()), 404);
        session(['admin_locale' => $locale]);
        auth()->user()?->update(['locale' => $locale]);

        return back();
    })->name('locale');

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'destroy'])->name('logout');

        Route::get('/', DashboardController::class)->name('dashboard');

        // Projects
        Route::post('projects/reorder', [ProjectController::class, 'reorder'])->name('projects.reorder');
        Route::patch('projects/{project}/toggle/{field}', [ProjectController::class, 'toggle'])->whereIn('field', ['status', 'is_featured'])->name('projects.toggle');
        Route::post('projects/{project}/duplicate', [ProjectController::class, 'duplicate'])->name('projects.duplicate');
        Route::resource('projects', ProjectController::class)->except('show');

        // Pages
        Route::resource('pages', PageController::class)->except('show');

        // Builder live preview
        Route::post('builder/preview', [BuilderController::class, 'preview'])->name('builder.preview');

        // Categories
        Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
        Route::patch('categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('categories/{category}/sub', [CategoryController::class, 'storeSub'])->name('categories.sub.store');
        Route::put('sub-categories/{subCategory}', [CategoryController::class, 'updateSub'])->name('categories.sub.update');
        Route::delete('sub-categories/{subCategory}', [CategoryController::class, 'destroySub'])->name('categories.sub.destroy');

        // Media library
        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::get('media/list', [MediaController::class, 'list'])->name('media.list');
        Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');
        Route::post('media/embed', [MediaController::class, 'embed'])->name('media.embed');
        Route::post('media/regenerate', [MediaController::class, 'regenerate'])->name('media.regenerate');
        Route::post('media/bulk-delete', [MediaController::class, 'bulkDestroy'])->name('media.bulk-destroy');
        Route::put('media/{media}', [MediaController::class, 'update'])->name('media.update');
        Route::post('media/{media}/poster', [MediaController::class, 'poster'])->name('media.poster');
        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

        // Messages
        Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::patch('messages/{message}/unread', [MessageController::class, 'unread'])->name('messages.unread');
        Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

        // Social links
        Route::post('socials/reorder', [SocialController::class, 'reorder'])->name('socials.reorder');
        Route::patch('socials/{social}/toggle', [SocialController::class, 'toggle'])->name('socials.toggle');
        Route::resource('socials', SocialController::class)->only(['index', 'store', 'update', 'destroy']);

        // Settings
        Route::get('settings/{tab?}', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings/{tab}', [SettingController::class, 'update'])->name('settings.update');

        // Profile
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'password'])->name('profile.password');
    });
});
