<?php

use Illuminate\Support\Facades\Route;

use Modules\Promotion\Http\Controllers\Backend\API\PromotionsController;


Route::get('get-promotion-list', [PromotionsController::class, 'getCouponsList']);
Route::get('get-promotion-details', [PromotionsController::class, 'getPromotionAndCouponDetails']);

Route::group(['middleware' => 'auth:sanctum'], function () {

});
