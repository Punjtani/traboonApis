<?php
use \Illuminate\Support\Facades\Route;
Route::group(['prefix'=>config('guide.guide_route_prefix')],function(){
    Route::get('/','GuideController@index')->name('guide.search'); // Search
    Route::get('/{slug}','GuideController@detail')->name('guide.detail');// Detail
});

Route::group(['prefix'=>'user/'.config('guide.guide_route_prefix')],function(){
    Route::match(['get','post'],'/','VendorController@index')->name('guide.vendor.index');
    Route::match(['get','post'],'/create','VendorController@create')->name('guide.vendor.create');
    Route::match(['get','post'],'/edit/{slug}','VendorController@edit')->name('guide.vendor.edit');
    Route::match(['get','post'],'/del/{slug}','VendorController@delete')->name('guide.vendor.delete');
    Route::match(['post'],'/store/{slug}','VendorController@store')->name('guide.vendor.store');
    Route::get('bulkEdit/{id}','VendorController@bulkEditGuide')->name("guide.vendor.bulk_edit");
    Route::get('/booking-report','VendorController@bookingReport')->name("guide.vendor.booking_report");
    Route::get('/booking-report/bulkEdit/{id}','VendorController@bookingReportBulkEdit')->name("guide.vendor.booking_report.bulk_edit");

    Route::group(['prefix'=>'availability'],function(){
        Route::get('/','AvailabilityController@index')->name('guide.vendor.availability.index');
        Route::get('/loadDates','AvailabilityController@loadDates')->name('guide.vendor.availability.loadDates');
        Route::match(['get','post'],'/store','AvailabilityController@store')->name('guide.vendor.availability.store');
    });

    Route::group(['prefix'=>'room'],function (){
        Route::get('{guide_id}/index','VendorRoomController@index')->name('guide.vendor.room.index');
        Route::get('{guide_id}/create','VendorRoomController@create')->name('guide.vendor.room.create');
        Route::get('{guide_id}/edit/{id}','VendorRoomController@edit')->name('guide.vendor.room.edit');
        Route::post('{guide_id}/store/{id}','VendorRoomController@store')->name('guide.vendor.room.store');

        Route::get('{guide_id}/del/{id}','VendorRoomController@delete')->name('guide.vendor.room.delete');

        Route::get('{guide_id}/bulkEdit/{id}','VendorRoomController@bulkEdit')->name('guide.vendor.room.bulk_edit');
    });

    Route::group(['prefix'=>'{guide_id}/availability'],function(){
        Route::get('/','AvailabilityController@index')->name('guide.vendor.room.availability.index');
        Route::get('/loadDates','AvailabilityController@loadDates')->name('guide.vendor.room.availability.loadDates');
        Route::match(['get','post'],'/store','AvailabilityController@store')->name('guide.vendor.room.availability.store');
    });

});

Route::post('guide/checkAvailability','GuideController@checkAvailability')->name('guide.checkAvailability');