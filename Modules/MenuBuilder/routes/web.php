<?php

use Illuminate\Support\Facades\Route;
use Modules\MenuBuilder\Http\Controllers\Backend\MenuBuildersController;

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
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => 'auth'], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend MenuBuilders Routes
     *
     * ---------------------------------------------------------------------
     */
    Route::group(['prefix' => 'menu', 'as' => 'menu.'], function () {
        Route::get('route_list', [MenuBuildersController::class, 'route_list'])->name('route_list');
        Route::get('title_list', [MenuBuildersController::class, 'menu_titles'])->name('title_list');
        Route::delete('delmenu/[id]', [MenuBuildersController::class, 'destroy']);
    });
    Route::post('menu-sequance', [MenuBuildersController::class, 'update_sequance'])->name('menu-sequance');
    Route::resource('menu', MenuBuildersController::class);
});
