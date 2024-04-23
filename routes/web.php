<?php

use App\Http\Controllers\Backend\AboutController;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\AdminProfileController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\DesignController;
use App\Http\Controllers\Backend\SettingController;

use App\Http\Controllers\Backend\SubCategoryController;
use App\Http\Controllers\Backend\ShowDesign;
use App\Http\Controllers\Backend\ShowDesignController;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::group(
    ['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'],
    function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        //profile routes _________________________________________________________________________________________
        Route::get('profile', [AdminProfileController::class, 'index'])->name('profile');
        Route::post('profile/update', [AdminProfileController::class, 'profileUpdate'])->name('profile.update');
        Route::post('profile/update/password', [AdminProfileController::class, 'passwordUpdate'])->name('password.update');

        //settigs _________________________________________________________________________________________
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('logo-setting-update', [SettingController::class, 'logoSettingUpdate'])->name('logo-setting-update.update');
        Route::put('general-settnig-update', [SettingController::class, 'generalSettingUpdate'])->name('general-setting-update.index');

        //category routes__________________________________________________________________________________________
        Route::put('category/change-status', [CategoryController::class, 'changeStatus'])->name('category.change-status');
        Route::resource('category', CategoryController::class);

        //sub category routes_______________________________________________________________________________________
        Route::put('sub-category/change-status', [SubCategoryController::class, 'changeStatus'])->name('sub-category.change-status');
        Route::resource('sub-category', SubCategoryController::class);

        //About______________________________________________________________________________
        Route::get('about', [AboutController::class, 'index'])->name('about.index');
        Route::put('about/update', [AboutController::class, 'update'])->name('about.update');

        //desgin _____________________________________________________________________________
        Route::delete('design/delete-design-video', [DesignController::class, 'deleteDesignVideo'])->name('design.delete-design-video');
        Route::delete('design/delete-design-image', [DesignController::class, 'deleteDesignImage'])->name('design.delete-design-image');
        Route::put('design/change-status', [DesignController::class, 'changeStatus'])->name('design.change-status');
        Route::resource('design', DesignController::class);

        //show desgins page____________________________________________________________________
        Route::get('show-designs', [ShowDesignController::class, 'index'])->name('show-designs.index');

        //child category routes_____________________________________________________________________________________
        Route::get('get-sub-categories', [SubCategoryController::class, 'getSubCategories'])->name('get-sub-categories');
    }
);

require __DIR__ . '/adminAuth.php';
