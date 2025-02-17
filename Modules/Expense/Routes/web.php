<?php

use Illuminate\Support\Facades\Route;
use Modules\Expense\Http\Controllers\ExpenseController;
use Modules\Expense\Http\Controllers\CategoryController;

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
     *  Backend Expense Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'expense', 'as' => 'expense.'], function () {
        Route::get('index_list', [ExpenseController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [ExpenseController::class, 'index_data'])->name('index_data');
        Route::get('trashed', [ExpenseController::class, 'trashed'])->name('trashed');
        Route::patch('trashed/{id}', [ExpenseController::class, 'restore'])->name('restore');
        Route::post('update-status/{id}', [ExpenseController::class, 'update_status'])->name('update_status');
        Route::post('bulk-action', [ExpenseController::class, 'bulk_action'])->name('bulk_action');
    });
    Route::resource('expense', ExpenseController::class);

    Route::group(['prefix' => 'expenses-categories', 'as' => 'expenses-categories.'], function () {
        Route::get('index_list', [CategoryController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [CategoryController::class, 'index_data'])->name('index_data');
        Route::get('export', [CategoryController::class, 'export'])->name('export');
        Route::post('bulk-action', [CategoryController::class, 'bulk_action'])->name('bulk_action');
        Route::post('update-status/{id}', [CategoryController::class, 'update_status'])->name('update_status');
    });
    Route::get('expenses-sub-categories.export', [CategoryController::class, 'subCategoryExport'])->name('expenses-sub-categories.export');
    Route::get('expenses-sub-categories', [CategoryController::class, 'index_nested'])->name('expenses-categories.index_nested');
    Route::get('expenses-sub-categories/index_nested_data', [CategoryController::class, 'index_nested_data'])->name('expenses-categories.index_nested_data');
    Route::resource('expenses-categories', CategoryController::class);
});

