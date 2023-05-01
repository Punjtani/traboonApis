<?php
use Illuminate\Support\Facades\Route;

Route::get('/','VirtualController@index')->name('virtual.admin.index');

Route::match(['get'],'/create','VirtualController@create')->name('virtual.admin.create');
Route::match(['get'],'/edit/{id}','VirtualController@edit')->name('virtual.admin.edit');

Route::post('/store/{id}','VirtualController@store')->name('virtual.admin.store');

Route::get('/getForSelect2','VirtualController@getForSelect2')->name('virtual.admin.getForSelect2');
Route::post('/bulkEdit','VirtualController@bulkEdit')->name('virtual.admin.bulkEdit');
