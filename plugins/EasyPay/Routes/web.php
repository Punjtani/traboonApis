<?php
use Illuminate\Support\Facades\Route;
Route::get('confirmEasyPay','EasyPayController@handleCheckout')->middleware('auth');