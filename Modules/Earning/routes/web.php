<?php

use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use Modules\Earning\Http\Controllers\Backend\EarningsController;

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
       *  Backend Earnings Routes
       *
       * ---------------------------------------------------------------------
       */

    Route::group(['prefix' => 'earnings', 'as' => 'earnings.'], function () {
        Route::get('index_data', [EarningsController::class, 'index_data'])->name('index_data');
        Route::get('get_search_data', [SearchController::class, 'get_search_data'])->name('get_search_data');
    });
    Route::resource('earnings', EarningsController::class);
});
