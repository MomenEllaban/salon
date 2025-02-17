<?php

use Illuminate\Support\Facades\Route;
use Modules\LikeModule\Http\Controllers\Backend\LikeModulesController;

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
     *  Backend LikeModules Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'likemodules', 'as' => 'likemodules.'], function () {
        Route::get('index_list', [LikeModulesController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [LikeModulesController::class, 'index_data'])->name('index_data');
        Route::get('export', [LikeModulesController::class, 'export'])->name('export');
    });
    Route::resource('likemodules', LikeModulesController::class);
});
