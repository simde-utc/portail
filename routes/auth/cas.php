<?php

// CAS custom routes
Route::get('login/cas/link', 'Auth\Cas\LinkToPasswordController@index')->name('cas.request');
