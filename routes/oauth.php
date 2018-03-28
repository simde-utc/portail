<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
| Attention !! Les routes sont préfixées avec 'oauth/'
*/


// Routes modifiées
Route::get('clients', '\App\Http\Controllers\Passport\ClientController@forUser')->middleware(['web', 'auth']);
Route::post('clients', '\App\Http\Controllers\Passport\ClientController@store')->middleware(['web', 'auth', 'admin']);
Route::put('clients/{client_id}', '\App\Http\Controllers\Passport\ClientController@update')->middleware(['web', 'auth', 'admin']);

Route::get('authorize', '\Laravel\Passport\Http\Controllers\AuthorizationController@authorize')->middleware(['web', 'auth', 'checkPassport', 'linkTokenToSession']);
Route::post('authorize', '\Laravel\Passport\Http\Controllers\ApproveAuthorizationController@approve')->middleware(['web', 'auth', 'linkTokenToSession']);

Route::post('token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken')->middleware(['throttle', 'checkPassport', 'linkTokenToSession']);

Route::post('personal-access-tokens', '\Laravel\Passport\Http\Controllers\PersonalAccessTokenController@store')->middleware(['web', 'auth', 'checkPassport']);

// Routes crées
Route::post('session', 'App\Http\Controllers\Passport\TokenController@create')->middleware('auth:api');
Route::get('session', 'App\Http\Controllers\Passport\TokenController@link')->middleware('web')->name('oauth/session');
