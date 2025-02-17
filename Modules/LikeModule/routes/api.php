<?php

use Modules\LikeModule\Http\Controllers\Backend\API\LikeController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('review-like', [LikeController::class, 'ReviewLike']);
});
