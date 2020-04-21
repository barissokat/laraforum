<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Auth::loginUsingId(1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/threads', ['as' => 'threads.index', 'uses' => 'ThreadController@index']);
Route::get('/threads/create', ['as' => 'threads.create', 'uses' => 'ThreadController@create']);
Route::get('/threads/{channel:slug}/{thread}', ['as' => 'threads.show', 'uses' => 'ThreadController@show']);
Route::post('/threads', ['as' => 'threads.store', 'uses' => 'ThreadController@store']);
Route::delete('/threads/{channel:slug}/{thread}', ['as' => 'threads.delete', 'uses' => 'ThreadController@destroy']);

Route::post('/threads/{channel:slug}/{thread}/replies', ['as' => 'replies.store', 'uses' => 'ReplyController@store']);
Route::patch('/replies/{reply}', ['as' => 'replies.update', 'uses' => 'ReplyController@update']);
Route::delete('/replies/{reply}', ['as' => 'replies.delete', 'uses' => 'ReplyController@destroy']);

Route::get('/threads/{channel:slug}', ['as' => 'channels.index', 'uses' => 'ChannelController@show']);

Route::post('/replies/{reply}/favorites', ['as' => 'replies.favorite', 'uses' => 'FavoriteController@store']);
Route::delete('/replies/{reply}/favorites', ['as' => 'replies.unfavorite', 'uses' => 'FavoriteController@destroy']);

Route::get('/profiles/{user:name}', ['as' => 'profiles.show', 'uses' => 'ProfileController@show']);
