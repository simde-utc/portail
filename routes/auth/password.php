<?php
/**
 * Email/password auth management routes.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

Route::get('reset', 'Auth\Password\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('reset', 'Auth\Password\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('reset/{token}', 'Auth\Password\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('reset/done', 'Auth\Password\ResetPasswordController@reset')->name('password.done');
