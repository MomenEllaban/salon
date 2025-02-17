<?php

use Illuminate\Support\Facades\Route;
use Modules\Logistic\Http\Controllers\Backend\LogisticsController;
use Modules\Logistic\Http\Controllers\Backend\LogisticZoneController;

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
     *  Backend Logistics Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'logistics', 'as' => 'logistics.'], function () {
        Route::get('index_list', [LogisticsController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [LogisticsController::class, 'index_data'])->name('index_data');
        Route::get('export', [LogisticsController::class, 'export'])->name('export');
        Route::post('bulk-action', [LogisticsController::class, 'bulk_action'])->name('bulk_action');
        Route::post('update-status/{id}', [LogisticsController::class, 'update_status'])->name('update_status');
    });
    Route::resource('logistics', LogisticsController::class);

    Route::group(['prefix' => 'logistic-zone', 'as' => 'logistic-zones.'], function () {
        Route::get('index_list', [LogisticZoneController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [LogisticZoneController::class, 'index_data'])->name('index_data');
        Route::get('export', [LogisticZoneController::class, 'export'])->name('export');
        Route::post('bulk-action', [LogisticZoneController::class, 'bulk_action'])->name('bulk_action');
        Route::post('update-status/{id}', [LogisticZoneController::class, 'update_status'])->name('update_status');
    });
    Route::resource('logistic-zones', LogisticZoneController::class);
});
