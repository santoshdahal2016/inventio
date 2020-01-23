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

Route::get('/','FrontendController@home')->name('frontend');



Route::get('/home', 'HomeController@index')->name('home');



Route::get('/api', function (){
    return null;
});
Auth::routes();

Route::namespace('Api')->prefix('api')->name('api.')->group(function () {

    /*Admin Route*/
    Route::resource('/users', 'UserController', ['except' => ['show', 'create', 'store']]);
    Route::resource('/categories', 'CategoryController', ['except' => ['show', 'create']]);

    Route::post('/categories/sort/{category1}/{category2}', 'CategoryController@sort')->name('category.sort');
    Route::resource('/projects', 'ProjectController', ['except' => ['create']]);

    /*User Route*/
    Route::get('/profile/get-me', 'ProfileController@getMe')->name('get.profile');
    Route::post('/profile/change-password', 'ProfileController@changePassword')->name('change.password');
    Route::post('/profile/change-email', 'ProfileController@changeEmail')->name('change.email');
});
