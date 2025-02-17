<?php

use Illuminate\Support\Facades\Route;
use Modules\Constant\Http\Controllers\Backend\ConstantsController;

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
     *  Backend Constants Routes
     *
     * ---------------------------------------------------------------------
     */
    Route::group(['prefix' => 'constants', 'as' => 'constants.'], function () {
        Route::get('/index_list', [ConstantsController::class, 'index_list'])->name('index_list');
        Route::get('/index_data', [ConstantsController::class, 'index_data'])->name('index_data');
        Route::get('/trashed', [ConstantsController::class, 'trashed'])->name('trashed');
        Route::patch('/trashed/{id}', [ConstantsController::class, 'restore'])->name('restore');
        Route::post('bulk-action', [ConstantsController::class, 'bulk_action'])->name('bulk_action');
        Route::post('update-status/{id}', [ConstantsController::class, 'update_status'])->name('update_status');
    });

    Route::resource('constants', ConstantsController::class);
});
