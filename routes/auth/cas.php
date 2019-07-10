<?php
/**
 * Cas Auth management routes.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

Route::get('link', 'Auth\Cas\LinkToPasswordController@index')->name('cas.request');
Route::post('link', 'Auth\Cas\LinkToPasswordController@store')->name('cas.link');
