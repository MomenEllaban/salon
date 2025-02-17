<?php

use Illuminate\Support\Facades\Route;
use Modules\Tip\Http\Controllers\Backend\TipsController;

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
     *  Backend Tips Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'tips', 'as' => 'tips'], function () {
        Route::get('index_list', [TipsController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [TipsController::class, 'index_data'])->name('index_data');
        Route::get('trashed', [TipsController::class, 'trashed'])->name('trashed');
        Route::patch('trashed/{id}', [TipsController::class, 'restore'])->name('restore');
    });
    Route::resource('tips', TipsController::class);
});
