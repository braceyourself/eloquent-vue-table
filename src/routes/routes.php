<?php
use Illuminate\Support\Facades\Route;

Route::get('{model}/meta', 'ModelDataController@meta');

Route::get('{namespace}/{model}', 'ModelDataController@index');
Route::post('{namespace}/{model}', 'ModelDataController@store');
Route::get('{namespace}/{model}/{id}', 'ModelDataController@show');
Route::match(['PUT','PATCH'],'{namespace}/{model}/{id}', 'ModelDataController@update');
Route::delete('{namespace}/{model}/{id}', 'ModelDataController@destroy');
Route::post('{namespace}/{model}/{id}/actions/{action}', 'ModelDataController@doAction');
