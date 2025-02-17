<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\Backend\EmployeesController;

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
     *  Backend Employees Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'employees', 'as' => 'employees.'], function () {
        Route::get('index_list', [EmployeesController::class, 'index_list'])->name('index_list');
        Route::get('commision_list', [EmployeesController::class, 'commision_list'])->name('commision_list');

        Route::get('employee_list', [EmployeesController::class, 'employee_list'])->name('employee_list');
        Route::post('change-password', [EmployeesController::class, 'change_password'])->name('change_password');
        Route::post('update-status/{id}', [EmployeesController::class, 'update_status'])->name('update_status');
        Route::post('block-employee/{id}', [EmployeesController::class, 'block_employee'])->name('block-employee');
        Route::post('verify-employee/{id}', [EmployeesController::class, 'verify_employee'])->name('verify-employee');
        Route::get('review_data', [EmployeesController::class, 'review_data'])->name('review_data');
        Route::delete('destroy_review/{id}', [EmployeesController::class, 'destroy_review'])->name('destroy_review');
        Route::get('index_data', [EmployeesController::class, 'index_data'])->name('index_data');
        Route::get('trashed', [EmployeesController::class, 'trashed'])->name('trashed');
        Route::get('trashed/{id}', [EmployeesController::class, 'restore'])->name('restore');
        Route::post('bulk-action', [EmployeesController::class, 'bulk_action'])->name('bulk_action');
        Route::post('bulk-action-review', [EmployeesController::class, 'bulk_action_review'])->name('bulk_action_review');
        Route::get('export', [EmployeesController::class, 'export'])->name('export');
        Route::get('review-export', [EmployeesController::class, 'reviewExport'])->name('reviewExport');
        Route::get('empolye-services/{id}', [EmployeesController::class, 'employeeServices'])->name('employee-services');
    });
    Route::get('employees-review', [EmployeesController::class, 'review'])->name('employees.review');
    Route::resource('employees', EmployeesController::class);
});
