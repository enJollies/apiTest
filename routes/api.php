<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['namespace' => 'Api'], function() {

    Route::group(['prefix' => 'users'], function() {
        Route::get('/', 'UserController@index')->name('users.index');
        Route::post('/', 'UserController@store')->name('users.store');
        Route::get('/{user}', 'UserController@show')->whereNumber('user')->name('users.show');
        Route::patch('/{user}', 'UserController@update')->whereNumber('user')->name('users.update');
        Route::delete('/{user}', 'UserController@destroy')->whereNumber('user')->name('users.destroy');
        Route::post('/subscribe', 'UserController@subscribe')->name('users.subscribe');
        Route::post('/unsubscribe', 'UserController@unsubscribe')->name('users.unsubscribe');
        Route::post('/unsubscribe/all', 'UserController@unsubscribeAll')->name('users.unsubscribeAll');

        Route::post('/show/sections', 'UserController@showSubSections')->whereNumber('user')->name('users.showSubSections');
    });

    Route::group(['prefix' => 'sections'], function() {
        Route::get('/', 'SectionController@index')->name('sections.index');
        Route::post('/', 'SectionController@store')->name('sections.store');
        Route::get('/{section}', 'SectionController@show')->whereNumber('section')->name('sections.show');
        Route::patch('/{section}', 'SectionController@update')->whereNumber('section')->name('sections.update');
        Route::delete('/{section}', 'SectionController@destroy')->whereNumber('section')->name('sections.destroy');

        Route::post('/show/subs', 'SectionController@showSubs')->whereNumber('section')->name('sections.showSubs')->middleware('jwt.auth');
    });
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});
