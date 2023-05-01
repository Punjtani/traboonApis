<?php
use Illuminate\Support\Facades\Route;

// Virtual
Route::group(['prefix'=>config('virtual.virtual_route_prefix')],function(){
    Route::get('/{slug}','VirtualController@detail')->name("virtual.detail");;// Detail
    Route::get('/search/searchForSelect2','VirtualController@searchForSelect2')->name("virtual.searchForSelect");;// Search for select 2
});
