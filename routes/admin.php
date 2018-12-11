<?php
/**
 * Gestion des routes admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

Route::get('/', 'HomeController@index');

Route::get('dashboard', 'DashboardController@index')->name('dashboard-index');

Route::get('auth/login', 'AuthController@getLogin');
Route::post('auth/login', 'AuthController@postLogin');
Route::get('auth/logout', 'AuthController@getLogout');

Route::get('charts', 'ChartsController@index')->name('charts-index');

Route::get('search', 'SearchController@index')->name('search-index');
Route::post('search', 'SearchController@search')->name('search-search');
Route::get('search/{user_id}', 'SearchController@show')->name('search-show');

Route::get('users', 'UserController@index')->name('users-index');
Route::get('users/{user_id}', 'UserController@show')->name('users-show');
Route::post('users/{user_id}/impersonate', 'UserController@impersonate')->name('users-impersonate');
Route::post('users/{user_id}/contributeBde', 'UserController@contributeBde')->name('users-contributeBde');

Route::resource('clients', 'ClientController');
