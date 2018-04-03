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
	Route::middleware(Scopes::matchOne('user-get-info-identity'))->get('/onescope', function (Illuminate\Http\Request $request) {
	    return $request->user();
	});
	Route::middleware(Scopes::matchAll(['user-get-info', 'user-get-assos-done']))->get('/allscopes', function (Illuminate\Http\Request $request) {
	    return $request->user();
	});
	Route::middleware(Scopes::matchAnyClient())->get('/any', function (Illuminate\Http\Request $request) {
	    return 'Sans aucun scope requis mais le token ne doit pas être relié à un utilisateur';
	});

	// Authentication Routes
	Route::get('login', 'LoginController@index')->middleware('guest')->name('api/login');
	Route::get('logout', 'LoginController@destroy')->middleware(Scopes::matchAnyUser())->name('api/logout');

	Route::get('/user', function (Illuminate\Http\Request $request) {
	    return $request->user();
	})->middleware(Scopes::matchAnyUser())->name('api/user');

	Route::apiResources([
	  'users'			=> 'UserController',
	  'assos' 			=> 'AssoController',
	  'assos/types' 	=> 'AssoTypeController',
	  'groups'      	=> 'GroupController',
	]);
});
