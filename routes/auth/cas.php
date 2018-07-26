<?php

// CAS custom routes /login/cas
Route::get('link', 'Auth\Cas\LinkToPasswordController@index')->name('cas.request');
Route::post('link', 'Auth\Cas\LinkToPasswordController@store')->name('cas.link');
