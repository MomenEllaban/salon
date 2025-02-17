<?php

use Modules\World\Http\Controllers\Backend\CityController;
use Modules\World\Http\Controllers\Backend\CountryController;
use Modules\World\Http\Controllers\Backend\StateController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('country-list', [CountryController::class, 'index_list']);
    Route::get('state-list', [StateController::class, 'index_list']);
    Route::get('city-list', [CityController::class, 'index_list']);
});
