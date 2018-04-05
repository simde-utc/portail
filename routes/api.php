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
	Route::get('user', 'ConnectedUserController@index')->middleware(Scopes::matchAnyUser())->name('api/user');
	Route::get('user/providers', 'ConnectedUserController@getProviders')->middleware(Scopes::matchOne('user-get-info-identity-auth'))->name('api/user/providers');
	Route::get('user/{name}', 'ConnectedUserController@getProvider')->middleware(Scopes::matchOne('user-get-info-identity-auth'))->name('api/user/provider');

	// Informations relatives au client
	Route::get('client', 'ClientController@index')->middleware(Scopes::matchAnyUserOrClient())->name('api/client');
	Route::get('client/users', 'ClientController@getUsers')->middleware(Scopes::matchAnyClient())->name('api/client/users');
	Route::get('client/{user_id}', 'ClientController@getUser')->middleware(Scopes::matchAnyClient())->name('api/client/user');
	Route::delete('client', 'ClientController@destroyCurrent')->middleware(Scopes::matchAnyUser())->name('api/client/delete');
	Route::delete('client/users', 'ClientController@destroyAll')->middleware(Scopes::matchAnyClient())->name('api/client/users/delete');
	Route::delete('client/{user_id}', 'ClientController@destroy')->middleware(Scopes::matchAnyClient())->name('api/client/user/delete');

	// Ressources diverses
	Route::apiResources([
	  'users'			=> 'UserController',
	  'assos' 			=> 'AssoController',
	  'assos/types' 	=> 'AssoTypeController',
	  'groups'      	=> 'GroupController',
	]);
});
