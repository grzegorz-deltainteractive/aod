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
    Route::get('suppliers/displaypools/{id}/{supplierId}/pdf', 'SuppliersPoolsController@displayPoolsPdf')->name('suppliers.displayPoolsPdf');
    Route::get('suppliers/listpools/{id}/{supplierId}', 'SuppliersPoolsController@listPools')->name('suppliers.listPools');
    Route::get('suppliers/listSupplierPools/{supplierId}', 'SuppliersPoolsController@listSupplierPools')->name('suppliers.listSupplierPools');
    Route::get('suppliers/singlepool/{id}/{supplierId}/{userId}', 'SuppliersPoolsController@singlePool')->name('suppliers.singlePool');
    Route::get('suppliers/displaypools/draws/{id}/{supplierId}/{parameterId}', 'SuppliersPoolsController@displayParameterDraw')->name('suppliers.displayParameterDraw');
    Route::any('suppliers/pools/{id}/fill/{poolId}', 'SuppliersPoolsController@fillPool')->name('suppliers.pools.fill');
    Route::any('suppliers/pools/{id}/fill/{poolId}/edit/{userId}', 'SuppliersPoolsController@editPool')->name('suppliers.pools.edit');
    Route::any('suppliers/pools/{id}/filled/{poolId}', 'SuppliersPoolsController@filledPools')->name('suppliers.pools.filled');
    Route::any('suppliers/pools/accept/{id}/{poolId}/{userId}', 'SuppliersPoolsController@acceptPool')->name('suppliers.pools.accept');
    Route::any('suppliers/pools/{id}/filled/{poolId}/user/{userId}', 'SuppliersPoolsController@filledPoolsSingle')->name('suppliers.pools.filled.single');
    Route::any('suppliers/pools/{id}/filled/{poolId}/user/{userId}/pdf', 'SuppliersPoolsController@filledPoolsSinglePdf')->name('suppliers.pools.filled.single.pdf');
    Route::any('suppliers/contacts/add/{id}', 'SuppliersController@addContact')->name('suppliers.contact.add');
    Route::any('suppliers/contacts/remove/{id}/{contactId}', 'SuppliersController@removeContact')->name('suppliers.contact.remove');
    Voyager::routes();
});
