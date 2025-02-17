<?php

use Illuminate\Support\Facades\Route;
use Modules\Location\Http\Controllers\Backend\LocationsController;
use Modules\Location\Http\Controllers\Backend\StocksController;

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
     *  Backend Locations Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'locations', 'as' => 'locations.'], function () {
        Route::get('index_list', [LocationsController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [LocationsController::class, 'index_data'])->name('index_data');
        Route::get('export', [LocationsController::class, 'export'])->name('export');
    });
    Route::group(['prefix' => 'stocks'], function () {
        Route::get('add', [StocksController::class, 'create'])->name('stocks.create');
    });
    Route::post('stock-add', [StocksController::class, 'store'])->name('stocks.store');
    Route::get('get-variation-stocks', [StocksController::class, 'getVariationStocks'])->name('stocks.getVariationStocks');
    Route::resource('locations', LocationsController::class);
});
