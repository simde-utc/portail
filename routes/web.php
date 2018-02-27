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
    return view('welcome');
});

Auth::routes();

// Authentication Routes
Route::get('login', 'Auth\LoginController@showLoginOptions')->name('login');
Route::get('login/cas', 'Auth\LoginController@showCasLoginForm')->name('login.cas');
Route::get('login/pass', 'Auth\LoginController@showPassLoginForm')->name('login.pass');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('home', 'HomeController@index')->name('home');
