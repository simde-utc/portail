<?php

// Password custom routes
Route::get('login/password/reset', 'Auth\Password\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('login/password/reset', 'Auth\Password\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('login/password/reset/{token}', 'Auth\Password\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('login/password/reset/done',  'Auth\Password\ResetPasswordController@reset')->name('password.done');
