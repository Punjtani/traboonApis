<?php
use \Illuminate\Support\Facades\Route;
Route::group(['prefix'=>config('activity.activity_route_prefix')],function(){
    Route::get('/','ActivityController@index')->name('activity.search'); // Search
    Route::get('/{slug}','ActivityController@detail')->name('activity.detail');// Detail
    Route::match(['get','post'],'/selected_activities','ActivityController@selected')->name('activity.selected');// Detail
});

Route::group(['prefix'=>'user/'.config('activity.activity_route_prefix')],function(){
    Route::match(['get','post'],'/','VendorController@index')->name('activity.vendor.index');
    Route::match(['get','post'],'/create','VendorController@create')->name('activity.vendor.create');
    Route::match(['get','post'],'/edit/{slug}','VendorController@edit')->name('activity.vendor.edit');
    Route::match(['get','post'],'/del/{slug}','VendorController@delete')->name('activity.vendor.delete');
    Route::match(['post'],'/store/{slug}','VendorController@store')->name('activity.vendor.store');
    Route::get('bulkEdit/{id}','VendorController@bulkEditActivity')->name("activity.vendor.bulk_edit");
    Route::get('/booking-report','VendorController@bookingReport')->name("activity.vendor.booking_report");
    Route::get('/booking-report/bulkEdit/{id}','VendorController@bookingReportBulkEdit')->name("activity.vendor.booking_report.bulk_edit");

    Route::group(['prefix'=>'availability'],function(){
        Route::get('/','AvailabilityController@index')->name('activity.vendor.availability.index');
        Route::get('/loadDates','AvailabilityController@loadDates')->name('activity.vendor.availability.loadDates');
        Route::match(['get','post'],'/store','AvailabilityController@store')->name('activity.vendor.availability.store');
    });

    Route::group(['prefix'=>'room'],function (){
        Route::get('{activity_id}/index','VendorRoomController@index')->name('activity.vendor.room.index');
        Route::get('{activity_id}/create','VendorRoomController@create')->name('activity.vendor.room.create');
        Route::get('{activity_id}/edit/{id}','VendorRoomController@edit')->name('activity.vendor.room.edit');
        Route::post('{activity_id}/store/{id}','VendorRoomController@store')->name('activity.vendor.room.store');

        Route::get('{activity_id}/del/{id}','VendorRoomController@delete')->name('activity.vendor.room.delete');

        Route::get('{activity_id}/bulkEdit/{id}','VendorRoomController@bulkEdit')->name('activity.vendor.room.bulk_edit');
    });

    Route::group(['prefix'=>'{activity_id}/availability'],function(){
        Route::get('/','AvailabilityController@index')->name('activity.vendor.room.availability.index');
        Route::get('/loadDates','AvailabilityController@loadDates')->name('activity.vendor.room.availability.loadDates');
        Route::match(['get','post'],'/store','AvailabilityController@store')->name('activity.vendor.room.availability.store');
    });

});

Route::post('activity/checkAvailability','ActivityController@checkAvailability')->name('activity.checkAvailability');
