<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Backend\BrandsController;
use Modules\Product\Http\Controllers\Backend\CategoryController;
use Modules\Product\Http\Controllers\Backend\OrdersController;
use Modules\Product\Http\Controllers\Backend\ProductsController;
use Modules\Product\Http\Controllers\Backend\ReviewController;
use Modules\Product\Http\Controllers\Backend\UnitsController;
use Modules\Product\Http\Controllers\Backend\VariationsController;

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
     *  Backend Products Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'variations', 'as' => 'variations.'], function () {
        Route::get('index_list', [VariationsController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [VariationsController::class, 'index_data'])->name('index_data');
        Route::get('export', [VariationsController::class, 'export'])->name('export');
        Route::post('update-status/{id}', [VariationsController::class, 'update_status'])->name('update_status');
        Route::post('bulk-action', [VariationsController::class, 'bulk_action'])->name('bulk_action');
    });

    Route::resource('variations', VariationsController::class);

    Route::group(['prefix' => 'units', 'as' => 'units.'], function () {
        Route::get('index_list', [UnitsController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [UnitsController::class, 'index_data'])->name('index_data');
        Route::get('export', [UnitsController::class, 'export'])->name('export');
        Route::post('update-status/{id}', [UnitsController::class, 'update_status'])->name('update_status');
        Route::post('bulk-action', [UnitsController::class, 'bulk_action'])->name('bulk_action');
    });
    Route::resource('units', UnitsController::class);

    Route::group(['prefix' => 'products-categories', 'as' => 'products-categories.'], function () {
        Route::get('index_list', [CategoryController::class, 'index_list'])->name('index_list');
        Route::get('brand_list', [BrandsController::class, 'index_list'])->name('brand_list');
        Route::get('index_data', [CategoryController::class, 'index_data'])->name('index_data');
        Route::get('export', [CategoryController::class, 'export'])->name('export');
        Route::post('bulk-action', [CategoryController::class, 'bulk_action'])->name('bulk_action');
        Route::post('update-status/{id}', [CategoryController::class, 'update_status'])->name('update_status');
    });
    Route::get('products-sub-categories.export', [CategoryController::class, 'subCategoryExport'])->name('products-sub-categories.export');
    Route::get('products-sub-categories', [CategoryController::class, 'index_nested'])->name('products-categories.index_nested');
    Route::get('products-sub-categories/index_nested_data', [CategoryController::class, 'index_nested_data'])->name('products-categories.index_nested_data');
    Route::resource('products-categories', CategoryController::class);

    Route::group(['prefix' => 'brands', 'as' => 'brands.'], function () {
        Route::get('index_list', [BrandsController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [BrandsController::class, 'index_data'])->name('index_data');
        Route::get('export', [BrandsController::class, 'export'])->name('export');
        Route::post('bulk-action', [BrandsController::class, 'bulk_action'])->name('bulk_action');
        Route::post('update-status/{id}', [BrandsController::class, 'update_status'])->name('update_status');
    });
    Route::resource('brands', BrandsController::class);

    Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
        Route::get('index_list', [ProductsController::class, 'index_list'])->name('index_list');
        Route::get('index_list_with_varient', [ProductsController::class, 'index_list_with_varient'])->name('index_list_with_varient');
        Route::get('index_data', [ProductsController::class, 'index_data'])->name('index_data');
        Route::get('export', [ProductsController::class, 'export'])->name('export');
        Route::post('bulk-action', [ProductsController::class, 'bulk_action'])->name('bulk_action');
        Route::post('update-status/{id}', [ProductsController::class, 'update_status'])->name('update_status');
        Route::post('update-is-featured/{id}', [ProductsController::class, 'update_is_featured'])->name('update_is_featured');
        Route::get('gallery-images/{id}', [ProductsController::class, 'getGalleryImages']);
        Route::post('gallery-images/{id}', [ProductsController::class, 'uploadGalleryImages']);
    });
    Route::resource('products', ProductsController::class);

    Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
        Route::get('index_list', [ReviewController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [ReviewController::class, 'index_data'])->name('index_data');
        Route::post('bulk-action', [ReviewController::class, 'bulk_action'])->name('bulk_action');
    });
    Route::resource('reviews', ReviewController::class);

    // orders
    Route::get('orders-detail', [OrdersController::class, 'show'])->name('orders.show');
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', [OrdersController::class, 'index'])->name('orders.index');
        Route::get('index_data', [OrdersController::class, 'index_data'])->name('orders.index_data');
        Route::post('update-payment-status', [OrdersController::class, 'updatePaymentStatus'])->name('orders.update_payment_status');
        Route::post('update-delivery-status', [OrdersController::class, 'updateDeliveryStatus'])->name('orders.update_delivery_status');
        Route::get('invoice-download/{id}', [OrdersController::class, 'downloadInvoice'])->name('orders.downloadInvoice');
        // Route::get('orders-invoice-download', [OrdersController::class, 'downloadInvoice'])->name('orders.downloadinvoice');

    });
});
