<?php

// Password custom routes /login/password
Route::get('reset', 'Auth\Password\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('reset', 'Auth\Password\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('reset/{token}', 'Auth\Password\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('reset/done',  'Auth\Password\ResetPasswordController@reset')->name('password.done');
