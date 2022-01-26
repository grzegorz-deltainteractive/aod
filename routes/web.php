<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'admin'], function () {
    Route::get('pools/categories/{id}', 'PoolsController@categories')->name('pools.categories');
    Route::post('pools/categoriesSave/{id}', 'PoolsController@categoriesSave')->name('pools.categories.save');
    Route::get('suppliers/pools/{id}', 'SuppliersPoolsController@pools')->name('suppliers.pools');
    Route::get('suppliers/displaypools/{id}/{supplierId}', 'SuppliersPoolsController@displayPools')->name('suppliers.displayPools');
    Route::any('suppliers/pools/{id}/fill/{poolId}', 'SuppliersPoolsController@fillPool')->name('suppliers.pools.fill');
    Route::any('suppliers/pools/{id}/filled/{poolId}', 'SuppliersPoolsController@filledPools')->name('suppliers.pools.filled');
    Route::any('suppliers/pools/{id}/filled/{poolId}/user/{userId}', 'SuppliersPoolsController@filledPoolsSingle')->name('suppliers.pools.filled.single');
    Voyager::routes();
});
