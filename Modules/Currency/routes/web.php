<?php

use Illuminate\Support\Facades\Route;
use Modules\Currency\Http\Controllers\Backend\CurrenciesController;

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
     *  Backend Currencies Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::prefix('currencies')->group(function () {
        Route::get('index_list', [CurrenciesController::class, 'index_list'])->name('currencies.index_list');
        Route::get('index_data', [CurrenciesController::class, 'index_data'])->name('currencies.index_data');
        Route::get('trashed', [CurrenciesController::class, 'trashed'])->name('currencies.trashed');
        Route::patch('trashed/{id}', [CurrenciesController::class, 'restore'])->name('currencies.restore');
    });
    Route::resource('currencies', CurrenciesController::class);
});
