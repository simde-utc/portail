<?php
/**
 * Gestion des routes web.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

/**
 * Gestion des authentifications.
 */

Route::get('login', 'Auth\LoginController@index')->name('login');
Route::get('login/captcha', 'Auth\LoginController@update')->name('login.captcha');
Route::get('login/{provider?}', 'Auth\LoginController@show')->name('login.show');
Route::match(['get', 'post'], 'login/{provider}/process', 'Auth\LoginController@store')->name('login.process');
Route::match(['get', 'post'], 'logout/{redirect?}', 'Auth\LoginController@destroy')->name('logout');


/*
 * Gestion des inscriptions.
 */

Route::get('register/{provider?}', 'Auth\RegisterController@show')->name('register.show');
Route::match(['get', 'post'], 'register/{provider?}/process', 'Auth\RegisterController@store')->name('register.process');


/*
 * Redirige le reste du flux (HTTP 404) vers React.
 */

Route::any('{whatever}', 'RenderReact')->where('whatever', '.*')->name('home');
