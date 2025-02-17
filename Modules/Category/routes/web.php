<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\Backend\CategoriesController;

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
     *  Backend Categories Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
        Route::get('index_list', [CategoriesController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [CategoriesController::class, 'index_data'])->name('index_data');
        Route::post('bulk-action', [CategoriesController::class, 'bulk_action'])->name('bulk_action');
        Route::post('update-status/{id}', [CategoriesController::class, 'update_status'])->name('update_status');
        Route::get('export', [CategoriesController::class, 'export'])->name('export');
    });
    Route::get('sub-categories.export', [CategoriesController::class, 'subCategoryExport'])->name('sub-categories.export');
    Route::get('sub-categories', [CategoriesController::class, 'index_nested'])->name('categories.index_nested');
    Route::get('sub-categories/index_nested_data', [CategoriesController::class, 'index_nested_data'])->name('categories.index_nested_data');
    Route::resource('categories', CategoriesController::class);
});
