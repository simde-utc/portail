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


// Auth::routes();		// TODO à enlever sur le long terme

// Password reset
Route::get('password/reset',  'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::match(['get', 'head'], 'password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
Route::get('password/done',  'Auth\ResetPasswordController@done');


// Authentication Routes
Route::get('login', 'Auth\LoginController@showLoginOptions')->name('login.show');
Route::get('login/{provider?}', 'Auth\LoginController@showLoginForm')->name('login');
Route::get('login/process/{provider}', 'Auth\LoginController@login')->name('login.process');		// Callback to login users back from API

// Route::get('login/cas', 'Auth\LoginController@showCasLoginForm')->name('login.cas');
// Route::get('login/pass', 'Auth\LoginController@showPassLoginForm')->name('login.pass');

Route::post('logout/{redirection?}', 'Auth\LoginController@logout')->name('logout');



// Vues temporaires, uniquement de l'affichage de liens
Route::get('/', function () {
    return view('welcome');
});
Route::get('home', 'HomeController@index')->name('home');



