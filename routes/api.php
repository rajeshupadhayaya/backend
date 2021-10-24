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
Route::post('sociallogin', 'API\UserController@sociallogin');
Route::post('register', 'API\UserController@register');
Route::get('getpost/{query?}', 'API\PostController@getPost');
Route::get('viewpost/{slug}', 'API\PostController@viewPost');
Route::get('search/{query?}', 'API\SearchController@findJob');
Route::post('verifyEmail', 'API\UserController@verifyEmail');
Route::group(['middleware' => 'auth:api'], function () {
	Route::get('validateuser', 'API\UserController@validateUser');
	Route::get('my-jobs', 'API\UserController@myPost');
	Route::post('logout', 'API\UserController@logout');
	Route::post('postdetails', 'API\PostController@getPostdetails');
	Route::post('createpost', 'API\PostController@createPost');
	Route::post('updatedetails', 'API\UserController@updateDetails');
	Route::post('change-password', 'API\UserController@updatePassword');
	Route::post('generateverifyemailcode', 'API\UserController@generateVerifyEmail');
	Route::post('admin/logout', 'API\AdminController@logout');
	Route::get('admin/getpost', 'API\AdminController@getPost');
	Route::get('admin/getviewrequest', 'API\AdminController@getViewRequest');
	Route::post('admin/updatepost', 'API\AdminController@updatePost');
	Route::post('admin/approveviewrequest', 'API\AdminController@approveViewRequest');
});
