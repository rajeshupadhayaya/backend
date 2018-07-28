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

  // Route::any('{path?}', function () {

  //   View::addExtension('html', 'php');

  //   return View::make('index');

  // })->where("path", "^((?!api).)*$");

Route::post('admin/login', 'API\AdminController@login');
Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::get('getpost/{query?}', 'API\PostController@getPost');
Route::get('viewpost/{query?}', 'API\PostController@viewPost');
Route::get('search/{query?}', 'API\SearchController@findJob');
Route::group(['middleware' => 'auth:api'], function(){
	Route::get('validateuser', 'API\UserController@validateUser');
	Route::post('details', 'API\UserController@details');
	Route::post('logout', 'API\UserController@logout');
	Route::post('postdetails', 'API\PostController@getPostdetails');
	Route::post('createpost', 'API\PostController@createPost');
	Route::post('admin/logout', 'API\AdminController@logout');
	Route::get('admin/getpost', 'API\AdminController@getPost');
	Route::get('admin/getviewrequest', 'API\AdminController@getViewRequest');
	Route::post('admin/approvepost', 'API\AdminController@approvePost');
	Route::post('admin/approveviewrequest', 'API\AdminController@approveViewRequest');
});
