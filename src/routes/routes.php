<?php
use Illuminate\Support\Facades\Route;

//dd(get_class($this));

Route::get('{model}/meta', 'ModelDataController@meta');

Route::get('{model}', 'ModelDataController@index');
Route::post('{model}', 'ModelDataController@store');
Route::get('{model}/{id}', 'ModelDataController@show');
Route::match(['PUT','PATCH'],'{model}/{id}', 'ModelDataController@update');
Route::delete('{model}/{id}', 'ModelDataController@destroy');
Route::post('{model}/{id}/actions/{action}', 'ModelDataController@doAction');
