<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\Backend\API\EmployeeController;

Route::get('employee-list', [EmployeeController::class, 'employeeList']);
Route::get('employee-detail', [EmployeeController::class, 'employeeDetail']);
Route::get('get-rating', [EmployeeController::class, 'getRating']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('employee-reviews', [EmployeeController::class, 'employeeList']);
    Route::post('save-rating', [EmployeeController::class, 'saveRating']);
    Route::post('delete-rating', [EmployeeController::class, 'deleteRating']);
});
