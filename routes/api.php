<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
    Route::resource('/stock', 'Api\StockController')->names([
        'index' => 'api.stock.index',
        'create' => 'api.stock.create',
        'store' => 'api.stock.store',
        'show' => 'api.stock.show',
        'edit' => 'api.stock.edit',
        'update' => 'api.stock.update',
        'destroy' => 'api.stock.destroy',
    ]);
});
