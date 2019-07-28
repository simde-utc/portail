<?php
/**
 * OAuth routes overlayer.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

/**
 * Lists scopes.
 */

Route::get('scopes', '\App\Services\Scopes@all');
Route::get('scopes/categories', '\App\Services\Scopes@getAllByCategories');


/*
 * Clients management.
 */

Route::get('clients', '\App\Http\Controllers\Passport\ClientController@index')
	->middleware(['forceJson', 'web', 'auth:web']);
Route::post('clients', '\App\Http\Controllers\Passport\ClientController@store')
	->middleware(['forceJson', 'web', 'auth:web', 'permission:client']);
Route::put('clients/{client_id}', '\App\Http\Controllers\Passport\ClientController@update')
	->middleware(['forceJson', 'web', 'auth:web', 'permission:client']);
Route::delete('clients/{client_id}', '\App\Http\Controllers\Passport\ClientController@destroy')
	->middleware(['forceJson', 'web', 'auth:web', 'permission:client']);


/*
 * Authorization route.
 */

Route::get('authorize', '\Laravel\Passport\Http\Controllers\AuthorizationController@authorize')
	->middleware(['web', 'auth:web', 'checkPassport']);


/*
 * Tokens management.
 */

Route::post('token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken')
	->middleware(['forceJson', 'throttle', 'checkPassport']);
Route::post('personal-access-tokens', '\Laravel\Passport\Http\Controllers\PersonalAccessTokenController@store')
	->middleware(['forceJson', 'web', 'auth:web', 'checkPassport']);
