<?php

use Illuminate\Support\Facades\Route;
use Modules\BussinessHour\Http\Controllers\Backend\BussinessHoursController;

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
     *  Backend BussinessHours Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'bussinesshours', 'as' => 'bussinesshours.'], function () {
        Route::get('index_list', [BussinessHoursController::class, 'index_list'])->name('index_list');
    });
    Route::resource('bussinesshours', BussinessHoursController::class);
});
