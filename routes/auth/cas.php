<?php

// CAS custom routes
Route::get('login/cas/link', 'Auth\Cas\LinkToPasswordController@index')->name('cas.request');
Route::post('login/cas/link', 'Auth\Cas\LinkToPasswordController@store')->name('cas.link');
