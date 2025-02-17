<?php

use Illuminate\Support\Facades\Route;
use Modules\Package\Http\Controllers\Backend\API\PackagesController;


Route::get('get-package-list', [PackagesController::class, 'PackageList']);
Route::get('get-user-package-list', [PackagesController::class, 'UserPackageList']);
Route::get('get-package-filter', [PackagesController::class, 'PackageFilter']);
Route::get('get-package-expiry-list', [PackagesController::class, 'PackageExpiry']);

Route::get('get-package', [PackagesController::class, 'Package']);
