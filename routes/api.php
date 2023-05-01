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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/test','Test@index');
// Hotel Apis
// Route::get('/','ApiHotelController@index')->name('hotel.admin.index');
// Route::get('/create','ApiHotelController@create')->name('hotel.admin.create');
// Route::get('/edit/{id}','ApiHotelController@edit')->name('hotel.admin.edit');
// Route::post('/store/{id}','ApiHotelController@store')->name('hotel.admin.store');
// Route::post('/bulkEdit','ApiHotelController@bulkEdit')->name('hotel.admin.bulkEdit');
Route::resource('hotel', ApiHotelController::class);
Route::resource('car', ApiCarController::class);
Route::post('car/search', 'ApiCarController@search');
Route::post('hotel/search', 'ApiHotelController@search');
Route::resource('tour', ApiTourController::class);
