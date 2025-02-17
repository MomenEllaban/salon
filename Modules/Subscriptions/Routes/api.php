<?php
use Illuminate\Support\Facades\Route;
use Modules\Subscriptions\Http\Controllers\Backend\API\PlanController;
use Modules\Subscriptions\Http\Controllers\Backend\API\PlanLimitationController;
use Modules\Subscriptions\Http\Controllers\Backend\API\SubscriptionController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/save-subscription_details', [SubscriptionController::class, 'saveSubscriptionDetails']);
    Route::get('/user-subscription_histroy', [SubscriptionController::class, 'getUserSubscriptionHistroy']);
    Route::post('/cancle-subscription', [SubscriptionController::class, 'cancelSubscription']);

    Route::apiResource('planlimitation', PlanLimitationController::class);
    Route::apiResource('plans', PlanController::class);
});
?>


