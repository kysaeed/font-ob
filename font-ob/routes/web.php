<?php

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
    return view('top');
});

// Auth::routes();


Route::group(['prefix' => 'home', 'middleware' => ['auth']], function() {
    Route::get('/', 'HomeController@index')->name('home');
});

Route::group(['prefix' => '-'], function() {
    Route::get('laod-stroke', 'TestController@loadStroke')->name('load_stroke');

    Route::get('test', 'TestController@test')->name('test');

    Route::get('cross', 'TestController@cross')->name('cross');

    Route::get('hello', function() {
        return 'Hello!';
    });
    Route::get('bye', function() {
        return 'bye!';
    });
});
