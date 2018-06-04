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
	Route::get('user', 'LoggedUserController@index')->middleware(Scopes::matchAnyUser())->name('api/user');
	Route::get('user/auths', 'LoggedUserController@getProviders')->middleware(Scopes::matchAnyUser())->name('api/user/auths');
	Route::get('user/auths/{name}', 'LoggedUserController@getProvider')->middleware(Scopes::matchAnyUser())->name('api/user/auth');

	// Gestions des authorisations données au client
	Route::get('client', 'ClientController@index')->middleware(Scopes::matchAnyUserOrClient())->name('api/client');
	Route::get('client/users', 'ClientController@getUsers')->middleware(Scopes::matchAnyClient())->name('api/client/users');
	Route::get('client/{user_id}', 'ClientController@getUser')->middleware(Scopes::matchAnyClient())->name('api/client/user');
	Route::delete('client', 'ClientController@destroyCurrent')->middleware(Scopes::matchAnyUser())->name('api/client/delete');
	Route::delete('client/users', 'ClientController@destroyAll')->middleware(Scopes::matchAnyClient())->name('api/client/users/delete');
	Route::delete('client/{user_id}', 'ClientController@destroy')->middleware(Scopes::matchAnyClient())->name('api/client/user/delete');

	// Ressources
	/*
		index : /{ressource} en GET
		show : /{ressource}/{id} en GET
		store : /{ressource} en POST
		update : /{ressource}/{id} en PUT
		destroy : /{ressource}/{id} en DELETE
	*/
	Route::apiResources([
		'{resource_type}/{resource_id}/contacts'	=> 'ContactController',
		'groups/{group_id}/members'					=> 'GroupMemberController',
		'groups'									=> 'GroupController',
		'assos'										=> 'AssoController',
		'assos/{asso_id}/members'					=> 'AssoMemberController',
		'assos/types'								=> 'AssoTypeController',
		'users'										=> 'UserController',
		'user/roles'								=> 'UserRoleController',
		'users/{user_id}/roles'						=> 'UserRoleController',
		'user/details'								=> 'UserDetailController',
		'users/{user_id}/details'					=> 'UserDetailController',
		'user/preferences'							=> 'UserPreferenceController',
		'users/{user_id}/preferences'				=> 'UserPreferenceController',
		'roles'										=> 'RoleController',
		'rooms'										=> 'RoomController',
		'partners'									=> 'PartnerController',
		'articles'									=> 'ArticleController',
		'events'									=> 'EventController',
		'visibilities'								=> 'VisibilityController',
  ]);
});
