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

# ThreadController
Route::get('/threads', ['as' => 'threads.index', 'uses' => 'ThreadController@index']);
Route::get('/threads/create', ['as' => 'threads.create', 'uses' => 'ThreadController@create']);
Route::get('/threads/{channel:slug}/{thread}', ['as' => 'threads.show', 'uses' => 'ThreadController@show']);
Route::post('/threads', ['as' => 'threads.store', 'uses' => 'ThreadController@store']);
Route::delete('/threads/{channel:slug}/{thread}', ['as' => 'threads.delete', 'uses' => 'ThreadController@destroy']);
# ReplyController
Route::get('/threads/{channel:slug}/{thread}/replies', ['as' => 'replies.index', 'uses' => 'ReplyController@index']);
Route::post('/threads/{channel:slug}/{thread}/replies', ['as' => 'replies.store', 'uses' => 'ReplyController@store']);
// Route::middleware('throttle:1')->post('/threads/{channel:slug}/{thread}/replies', ['as' => 'replies.store', 'uses' => 'ReplyController@store']);
Route::patch('/replies/{reply}', ['as' => 'replies.update', 'uses' => 'ReplyController@update']);
Route::delete('/replies/{reply}', ['as' => 'replies.delete', 'uses' => 'ReplyController@destroy']);
# ThreadSubscriptionController
Route::post('/threads/{channel:slug}/{thread}/subscriptions', ['as' => 'subscriptions.store', 'uses' => 'ThreadSubscriptionController@store']);
Route::delete('/threads/{channel:slug}/{thread}/subscriptions', ['as' => 'subscriptions.destroy', 'uses' => 'ThreadSubscriptionController@destroy']);
# ChannelController
Route::get('/threads/{channel:slug}', ['as' => 'channels.index', 'uses' => 'ChannelController@show']);
# FavoriteController
Route::post('/replies/{reply}/favorites', ['as' => 'replies.favorite', 'uses' => 'FavoriteController@store']);
Route::delete('/replies/{reply}/favorites', ['as' => 'replies.unfavorite', 'uses' => 'FavoriteController@destroy']);
# ProfileController
Route::get('/profiles/{user:name}', ['as' => 'profiles.show', 'uses' => 'ProfileController@show']);
## Api
# UserNotificationController
Route::get('/profiles/{user:name}/notifications', ['as' => 'notifications.index', 'uses' => 'Api\UserNotificationController@index']);
Route::delete('/profiles/{user:name}/notifications/{notification}', ['as' => 'notifications.destroy', 'uses' => 'Api\UserNotificationController@destroy']);
# UsersController
Route::get('/api/users', 'Api\UsersController@index');
# UserAvatarController
Route::post('/api/users/{user}/avatar', 'Api\UserAvatarController@store');
