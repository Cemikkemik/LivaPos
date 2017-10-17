<?php
use Pecee\SimpleRouter\SimpleRouter as Route;

Route::get( 'nexo/about', 'NexoAboutController@index' );
Route::get( 'nexo/pos', 'NexoRegistersController@__use' );
Route::get( 'nexo/settings/{page}', 'NexoSettingsController@settings' );
Route::get( 'nexo/settings', 'NexoSettingsController@settings' );
Route::get( 'nexo/sales', 'NexoCommandesController@lists' );
Route::get( 'nexo/stores', 'NexoStoreController@lists' );
Route::get( 'nexo/stores/add', 'NexoStoreController@add' );
Route::get( 'nexo/stores/all', 'NexoStoreController@all' );
Route::get( 'nexo/stores/{id}', 'NexoStoreController@stores' )->where([ 'id' => '[0-9]+']);
Route::get( 'nexo/stores/{param}/{id?}', 'NexoStoreController@lists' );
Route::match([ 'post' ], '/nexo/stores/{param}', 'NexoStoreController@lists' );
Route::get( 'nexo/about', 'NexoAboutController@index' );
Route::get( 'nexo/about', 'NexoAboutController@index' );
Route::get( 'nexo/about', 'NexoAboutController@index' );
Route::get( 'nexo/about', 'NexoAboutController@index' );
Route::get( 'nexo/about', 'NexoAboutController@index' );
Route::get( 'nexo/about', 'NexoAboutController@index' );
Route::get( 'nexo/about', 'NexoAboutController@index' );
Route::get( 'nexo/about', 'NexoAboutController@index' );
Route::get( 'nexo/about', 'NexoAboutController@index' );
Route::get( 'nexo/about', 'NexoAboutController@index' );
Route::get( 'nexo/about', 'NexoAboutController@index' );
Route::get( 'nexo/about', 'NexoAboutController@index' );