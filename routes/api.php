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
| Attention !! Les routes sont préfixées avec 'api/'
*/

Route::prefix('v1')->group(function () {
	// Connexions
	Route::get('login', 'LoginController@index')->middleware('guest')->name('api/login');
	Route::get('logout', 'LoginController@destroy')->middleware(Scopes::matchAnyUser())->name('api/logout');

	// Informations relatives à l'utlisateur
	Route::get('/user', 'ConnectedUserController@index')->middleware(Scopes::matchAnyUser())->name('api/user');
	Route::get('/user/providers', 'ConnectedUserController@getProviders')->middleware(Scopes::matchOne('user-get-info-identity-auth'))->name('api/user');
	Route::get('/user/{name}', 'ConnectedUserController@getProvider')->middleware(Scopes::matchOne('user-get-info-identity-auth'))->name('api/user');

	// Ressources diverses
	Route::apiResources([
	  'users'			=> 'UserController',
	  'assos' 			=> 'AssoController',
	  'assos/types' 	=> 'AssoTypeController',
	  'groups'      	=> 'GroupController',
	]);
});
