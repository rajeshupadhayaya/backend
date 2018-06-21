<?php

use Illuminate\Http\Request;
// use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::get('getpost/{query?}', 'API\PostController@getPost');
Route::get('search/{query?}', 'API\SearchController@findJob');
Route::group(['middleware' => 'auth:api'], function(){
	Route::post('details', 'API\UserController@details');
	Route::post('logout', 'API\UserController@logout');
	Route::post('postdetails', 'API\PostController@getPostdetails');
	Route::post('createpost', 'API\PostController@createPost');
});
