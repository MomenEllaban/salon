<?php

use Illuminate\Support\Facades\Route;
use Modules\Package\Http\Controllers\Backend\PackagesController;

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
     *  Backend Packages Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'package', 'as' => 'package.'], function () {
        Route::get('index_list', [PackagesController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [PackagesController::class, 'index_data'])->name('index_data');
        Route::get('export', [PackagesController::class, 'export'])->name('export');
        Route::post('bulk-action', [PackagesController::class, 'bulk_action'])->name('bulk_action');
        Route::post('update-status/{id}', [PackagesController::class, 'update_status'])->name('update_status');
        Route::post('update-is-featured/{id}', [PackagesController::class, 'update_is_featured'])->name('update_is_featured');
        Route::get('/services-index_list', [PackagesController::class, 'services_index_list'])->name('services_index_list');
        Route::get('clientPackage', [PackagesController::class, 'clientView'])->name('clientPackage');
        Route::get('clientpackagedata', [PackagesController::class, 'clientPackageData'])->name('clientPackageData');
        Route::get('{id}/client_package',[PackagesController::class, 'clientPackageView'])->name('clientPackageView');
        Route::get('user_package_list/{user_id}', [PackagesController::class, 'userPackageList'])->name('user_package_list');
    });
    Route::resource('package', PackagesController::class);
    Route::get('client-package', [PackagesController::class, 'clientView'])->name('clientPackage');
});
