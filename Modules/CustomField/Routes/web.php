<?php

use Modules\CustomField\Http\Controllers\Backend\CustomFieldController;

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
     *  Backend NotificationTemplates Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'customfield', 'as' => 'customfield.'], function () {
        Route::get('index_list', [CustomFieldController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [CustomFieldController::class, 'index_data'])->name('index_data');
    });

    Route::resource('customfield', CustomFieldController::class);
});
