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

Route::get('search', 'SearchController@index')->name('search-index');
Route::get('search/{user_id}', 'SearchController@show')->name('search-show');
Route::post('search', 'SearchController@search')->name('search-search');
Route::post('search/{user_id}', 'SearchController@impersonate')->name('search-impersonate');
