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

Route::get('resources/users', 'Resource\\UserController@index')->name('users-index');
Route::get('resources/users/{user_id}', 'Resource\\UserController@show')->name('users-show');
Route::post('resources/users/{user_id}/impersonate', 'Resource\\UserController@impersonate')->name('users-impersonate');
Route::post('resources/users/{user_id}/contributeBde', 'Resource\\UserController@contributeBde')->name('users-contributeBde');

Route::resource('resources/access', 'Resource\\AccessController');
Route::resource('resources/articles', 'Resource\\ArticleController');
Route::resource('resources/article-actions', 'Resource\\ArticleActionController');
Route::resource('resources/assos', 'Resource\\AssoController');
Route::resource('resources/asso-access', 'Resource\\AssoAccessController');
Route::resource('resources/asso-types', 'Resource\\AssoTypeController');
Route::resource('resources/clients', 'Resource\\ClientController');
