<?php

use Illuminate\Support\Facades\Route;
use Modules\Booking\Http\Controllers\Backend\API\BookingsController;
use Modules\Booking\Http\Controllers\Backend\API\PaymentController;

Route::get('booking-status', [BookingsController::class, 'statusList']);
Route::get('booking-invoice-download', [Modules\Booking\Http\Controllers\Backend\BookingsController::class, 'downloadInvoice'])->name('bookings.downloadinvoice');
Route::group(['middleware' => 'auth:sanctum', 'as' => 'backend.'], function () {
    Route::apiResource('bookings', BookingsController::class);
    Route::post('booking-update', [BookingsController::class, 'update']);
    Route::get('booking-list', [BookingsController::class, 'bookingList']);
    Route::get('booking-detail', [BookingsController::class, 'bookingDetail']);
    Route::get('search-booking', [BookingsController::class, 'searchBookings']);
    Route::post('save-booking', [BookingsController::class, 'store']);
    Route::post('save-payment', [PaymentController::class, 'savePayment']);
});
