<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\Backend\CustomersController;

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
     *  Backend Customers Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'customers', 'as' => 'customers.'], function () {
        Route::get('index_list', [CustomersController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [CustomersController::class, 'index_data'])->name('index_data');
        Route::get('trashed', [CustomersController::class, 'trashed'])->name('trashed');
        Route::get('trashed/{id}', [CustomersController::class, 'restore'])->name('restore');
        Route::post('bulk-action', [CustomersController::class, 'bulk_action'])->name('bulk_action');
        Route::post('change-password', [CustomersController::class, 'change_password'])->name('change_password');
        Route::post('update-status/{id}', [CustomersController::class, 'update_status'])->name('update_status');
        Route::post('block-customer/{id}', [CustomersController::class, 'block_customer'])->name('block-customer');
        Route::post('verify-customer/{id}', [CustomersController::class, 'verify_customer'])->name('verify-customer');
        Route::get('export', [CustomersController::class, 'export'])->name('export');
        Route::post('unique_email', [CustomersController::class, 'uniqueEmail'])->name('unique_email');

    });
    Route::resource('customers', CustomersController::class);
});
