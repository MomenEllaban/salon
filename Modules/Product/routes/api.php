<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Backend\API\BrandsController;
use Modules\Product\Http\Controllers\Backend\API\CartController;
use Modules\Product\Http\Controllers\Backend\API\CategoryController;
use Modules\Product\Http\Controllers\Backend\API\DashboardController;
use Modules\Product\Http\Controllers\Backend\API\OrdersController;
use Modules\Product\Http\Controllers\Backend\API\ProductsController;
use Modules\Product\Http\Controllers\Backend\API\ReviewController;
use Modules\Product\Http\Controllers\Backend\API\UnitsController;
use Modules\Product\Http\Controllers\Backend\API\WishListController;

Route::get('get-product-category', [CategoryController::class, 'categoryList']);
Route::get('get-product-list', [ProductsController::class, 'ProductList']);
Route::get('product_detail', [ProductsController::class, 'product_detail']);
Route::get('product-brand', [BrandsController::class, 'product_brand']);
Route::get('product-unit', [UnitsController::class, 'product_unit']);
Route::get('product-dashboard', [DashboardController::class, 'productDashboard']);
Route::get('get-review-list', [ReviewController::class, 'getReviewList']);
Route::get('get-order-status-list', [OrdersController::class, 'statusList']);
Route::get('order-invoice-download', [Modules\Product\Http\Controllers\Backend\OrdersController::class, 'OrderInvoicedownload'])->name('order.OrderInvoicedownload');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('add-to-cart', [CartController::class, 'store']);
    Route::get('get-cart-list', [CartController::class, 'getCartList']);
    Route::get('remove-cart', [CartController::class, 'removeCart']);
    Route::post('update-cart', [CartController::class, 'UpdateCart']);

    Route::post('add-to-wishlist', [WishListController::class, 'store']);
    Route::get('get-wishlist', [WishListController::class, 'getWishList']);
    Route::get('remove-wishlist', [WishListController::class, 'removeWishList']);

    Route::post('add-review', [ReviewController::class, 'store']);
    Route::get('remove-review', [ReviewController::class, 'removeReview']);
    Route::post('update-review', [ReviewController::class, 'UpdateReview']);

    Route::post('place-order', [OrdersController::class, 'store']);

    Route::get('get-order-list', [OrdersController::class, 'orderList']);
    Route::get('cancle-order', [OrdersController::class, 'cancleOrder']);
    Route::get('get-order-details', [OrdersController::class, 'orderDetails']);

    // Route::apiResource('category', CategoryController::class);
    // Route::post('category-detail', [CategoryController::class, 'categoryDetails']);
    // Route::get('subcategory-list', [CategoryController::class, 'subCategoryList']);
    // Route::post('subcategory-detail', [CategoryController::class, 'subCategoryDetail']);
    // Route::get('subcategories', [CategoryController::class, 'index_SubCategory']);
});
