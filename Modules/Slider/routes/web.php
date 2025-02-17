<?php

use Illuminate\Support\Facades\Route;
use Modules\Slider\Http\Controllers\Backend\SlidersController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
*
* Backend Routes
*
* --------------------------------------------------------------------
*/
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend Sliders Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'app-banners', 'as' => 'app_banners.'], function () {
        Route::get('index_list', [SlidersController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [SlidersController::class, 'index_data'])->name('index_data');
        Route::get('trashed', [SlidersController::class, 'trashed'])->name('trashed');
        Route::patch('trashed/{id}', [SlidersController::class, 'restore'])->name('restore');
        Route::post('update-status/{id}', [SlidersController::class, 'update_status'])->name('update_status');
        Route::post('bulk-action', [SlidersController::class, 'bulk_action'])->name('bulk_action');
        Route::post('uniqueSliders', [SlidersController::class, 'uniqueSliders'])->name('uniqueSliders');
    });
    Route::resource('app-banners', SlidersController::class);
});
