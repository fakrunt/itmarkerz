<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'QuestionController@index');

Route::get('home', 'HomeController@index');

Route::get('questions', 'QuestionController@index');
Route::get('question/add', 'QuestionController@add');
Route::post('question/add', 'QuestionController@add');
Route::get('question/getAnswer', 'QuestionController@getAnswer');
Route::post('question/addAnswer', 'QuestionController@addAnswer');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
