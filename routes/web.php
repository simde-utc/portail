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
//Auth::routes();
// Password reset
Route::get('password/reset',  'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::match(['get', 'head'], 'password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
Route::get('password/done',  'Auth\ResetPasswordController@done');

// Authentication Routes
Route::get('login', 'Auth\LoginController@index')->name('login');
Route::get('login/{provider?}', 'Auth\LoginController@show')->name('login.show');
Route::match(['get', 'post'], 'login/{provider}/process', 'Auth\LoginController@login')->name('login.process');
Route::match(['get', 'post'], 'logout/{redirection?}', 'Auth\LoginController@logout')->name('logout');

Route::get('register/{provider?}', 'Auth\RegisterController@show')->name('register.show');
Route::post('register/{provider?}/process', 'Auth\RegisterController@register')->name('register.process');

// Vues temporaires, uniquement de l'affichage de liens
Route::get('/', function () {
    return view('welcome');
});
Route::get('home', 'HomeController@index')->name('home');
