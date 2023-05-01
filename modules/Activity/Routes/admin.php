<?php
use \Illuminate\Support\Facades\Route;
Route::get('/','ActivityController@index')->name('activity.admin.index');
Route::get('/create','ActivityController@create')->name('activity.admin.create');
Route::get('/edit/{id}','ActivityController@edit')->name('activity.admin.edit');
Route::post('/store/{id}','ActivityController@store')->name('activity.admin.store');
Route::post('/bulkEdit','ActivityController@bulkEdit')->name('activity.admin.bulkEdit');

Route::group(['prefix'=>'attribute'],function (){
    Route::get('/','AttributeController@index')->name('activity.admin.attribute.index');
    Route::get('edit/{id}','AttributeController@edit')->name('activity.admin.attribute.edit');
    Route::post('store/{id}','AttributeController@store')->name('activity.admin.attribute.store');

    Route::get('terms/{id}','AttributeController@terms')->name('activity.admin.attribute.term.index');
    Route::get('term_edit/{id}','AttributeController@term_edit')->name('activity.admin.attribute.term.edit');
    Route::get('term_store','AttributeController@term_store')->name('activity.admin.attribute.term.store');

    Route::get('getForSelect2','AttributeController@getForSelect2')->name('activity.admin.attribute.term.getForSelect2');
    Route::get('getAttributeForSelect2','AttributeController@getAttributeForSelect2')->name('activity.admin.attribute.getForSelect2');
});
Route::group(['prefix'=>'room'],function (){

    Route::group(['prefix'=>'attribute'],function (){
        Route::get('/','RoomAttributeController@index')->name('activity.admin.room.attribute.index');
        Route::get('edit/{id}','RoomAttributeController@edit')->name('activity.admin.room.attribute.edit');
        Route::post('store/{id}','RoomAttributeController@store')->name('activity.admin.room.attribute.store');
        Route::post('editAttrBulk','RoomAttributeController@editAttrBulk')->name('activity.admin.room.attribute.editAttrBulk');

        Route::get('terms/{id}','RoomAttributeController@terms')->name('activity.admin.room.attribute.term.index');
        Route::get('term_edit/{id}','RoomAttributeController@term_edit')->name('activity.admin.room.attribute.term.edit');
        Route::get('term_store','RoomAttributeController@term_store')->name('activity.admin.room.attribute.term.store');

        Route::get('getForSelect2','RoomAttributeController@getForSelect2')->name('activity.admin.room.attribute.term.getForSelect2');
    });

    Route::get('{activity_id}/index','RoomController@index')->name('activity.admin.room.index');
    Route::get('{activity_id}/create','RoomController@create')->name('activity.admin.room.create');
    Route::get('{activity_id}/edit/{id}','RoomController@edit')->name('activity.admin.room.edit');
    Route::post('{activity_id}/store/{id}','RoomController@store')->name('activity.admin.room.store');


    Route::post('/bulkEdit','RoomController@bulkEdit')->name('activity.admin.room.bulkEdit');

});

Route::group(['prefix'=>'{activity_id}/availability'],function(){
    Route::get('/','AvailabilityController@index')->name('activity.admin.activity.availability.index');
    Route::get('/loadDates','AvailabilityController@loadDates')->name('activity.admin.room.availability.loadDates');
    Route::match(['get','post'],'/store','AvailabilityController@store')->name('activity.admin.room.availability.store');
});

