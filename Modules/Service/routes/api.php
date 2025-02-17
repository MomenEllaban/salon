<?php
use Illuminate\Support\Facades\Route;
use Modules\Service\Http\Controllers\Backend\API\ServiceController;

Route::get('service-list', [ServiceController::class, 'serviceList']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('service/staff/{id}', [ServiceController::class, 'assign_employee_list']);
    Route::post('service/staff/{id}', [ServiceController::class, 'assign_employee_update']);

    Route::get('service/branch/{id}', [ServiceController::class, 'assign_branch_list']);
    Route::post('service/branch/{id}', [ServiceController::class, 'assign_branch_update']);

    // Gallery Images
    Route::get('service-gallery', [ServiceController::class, 'ServiceGallery']);
    Route::post('/gallery-images/{id}', [ServiceController::class, 'uploadGalleryImages']);

    Route::apiResource('service', ServiceController::class);

    Route::post('service-detail', [ServiceController::class, 'serviceDetails']);
    Route::get('search-service', [ServiceController::class, 'searchServices']);
});
?>


