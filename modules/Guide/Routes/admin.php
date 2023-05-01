<?php
use \Illuminate\Support\Facades\Route;
Route::get('/','GuideController@index')->name('guide.admin.index');
Route::get('/create','GuideController@create')->name('guide.admin.create');
Route::get('/edit/{id}','GuideController@edit')->name('guide.admin.edit');
Route::post('/store/{id}','GuideController@store')->name('guide.admin.store');
Route::post('/bulkEdit','GuideController@bulkEdit')->name('guide.admin.bulkEdit');

Route::group(['prefix'=>'attribute'],function (){
    Route::get('/','AttributeController@index')->name('guide.admin.attribute.index');
    Route::get('edit/{id}','AttributeController@edit')->name('guide.admin.attribute.edit');
    Route::post('store/{id}','AttributeController@store')->name('guide.admin.attribute.store');

    Route::get('terms/{id}','AttributeController@terms')->name('guide.admin.attribute.term.index');
    Route::get('term_edit/{id}','AttributeController@term_edit')->name('guide.admin.attribute.term.edit');
    Route::get('term_store','AttributeController@term_store')->name('guide.admin.attribute.term.store');

    Route::get('getForSelect2','AttributeController@getForSelect2')->name('guide.admin.attribute.term.getForSelect2');
    Route::get('getAttributeForSelect2','AttributeController@getAttributeForSelect2')->name('guide.admin.attribute.getForSelect2');
});
Route::group(['prefix'=>'room'],function (){

    Route::group(['prefix'=>'attribute'],function (){
        Route::get('/','RoomAttributeController@index')->name('guide.admin.room.attribute.index');
        Route::get('edit/{id}','RoomAttributeController@edit')->name('guide.admin.room.attribute.edit');
        Route::post('store/{id}','RoomAttributeController@store')->name('guide.admin.room.attribute.store');
        Route::post('editAttrBulk','RoomAttributeController@editAttrBulk')->name('guide.admin.room.attribute.editAttrBulk');

        Route::get('terms/{id}','RoomAttributeController@terms')->name('guide.admin.room.attribute.term.index');
        Route::get('term_edit/{id}','RoomAttributeController@term_edit')->name('guide.admin.room.attribute.term.edit');
        Route::get('term_store','RoomAttributeController@term_store')->name('guide.admin.room.attribute.term.store');

        Route::get('getForSelect2','RoomAttributeController@getForSelect2')->name('guide.admin.room.attribute.term.getForSelect2');
    });

    Route::get('{guide_id}/index','RoomController@index')->name('guide.admin.room.index');
    Route::get('{guide_id}/create','RoomController@create')->name('guide.admin.room.create');
    Route::get('{guide_id}/edit/{id}','RoomController@edit')->name('guide.admin.room.edit');
    Route::post('{guide_id}/store/{id}','RoomController@store')->name('guide.admin.room.store');


    Route::post('/bulkEdit','RoomController@bulkEdit')->name('guide.admin.room.bulkEdit');

});

Route::group(['prefix'=>'{guide_id}/availability'],function(){
    Route::get('/','AvailabilityController@index')->name('guide.admin.guide.availability.index');
    Route::get('/loadDates','AvailabilityController@loadDates')->name('guide.admin.room.availability.loadDates');
    Route::match(['get','post'],'/store','AvailabilityController@store')->name('guide.admin.room.availability.store');
});

