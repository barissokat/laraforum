<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/threads/{thread}', ['as' => 'threads.show', 'uses' => 'ThreadController@show']);

Route::post('/threads/{thread}/replies', ['as' => 'replies.store', 'uses' => 'ReplyController@store']);