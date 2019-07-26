<?php
/**
 * Web routes management.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

/**
 * Authentifications management.
 */

Route::get('login', 'Auth\LoginController@index')->name('login');
Route::get('login/captcha', 'Auth\LoginController@update')->name('login.captcha');
Route::get('login/{provider?}', 'Auth\LoginController@show')->name('login.show');
Route::match(['get', 'post'], 'login/{provider}/process', 'Auth\LoginController@store')->name('login.process');
Route::match(['get', 'post'], 'logout/{redirect?}', 'Auth\LoginController@destroy')->name('logout');


/*
 * Registering management.
 */

Route::get('register/{provider?}', 'Auth\RegisterController@show')->name('register.show');
Route::match(['get', 'post'], 'register/{provider?}/process', 'Auth\RegisterController@store')->name('register.process');


/*
 * Old portal's routes management.
 */

Route::any('asso', 'OldToNewPortailController@asso');
Route::any('asso/{login}', 'OldToNewPortailController@assoLogin');
Route::any('asso/articles/{login}', 'OldToNewPortailController@assoArticlesLogin');
Route::any('article', 'OldToNewPortailController@article');
Route::any('article/show/{whatever}', 'OldToNewPortailController@article')->where('whatever', '.*');
Route::any('event', 'OldToNewPortailController@event');
Route::any('event/calendar', 'OldToNewPortailController@event');
Route::any('event/show/{whatever}', 'OldToNewPortailController@event')->where('whatever', '.*');

/*
 * Redirects the rest to React (HTTP 404).
 */

Route::any('{whatever}', 'RenderReact')->where('whatever', '.*')->name('home');
