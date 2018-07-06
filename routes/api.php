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
	Route::get('user', 'UserController@show')->middleware(Scopes::matchAnyUser())->name('api/user');
	Route::patch('user', 'UserController@update')->middleware(Scopes::matchAnyUser())->name('api/user/update');

	// Gestions des authorisations données au client
	Route::get('client', 'ClientController@index')->middleware(Scopes::matchAnyUserOrClient())->name('api/client');
	Route::get('client/users', 'ClientController@getUsersClient')->middleware(Scopes::matchAnyClient())->name('api/client/users');
	Route::get('client/{user_id}', 'ClientController@getUserClient')->middleware(Scopes::matchAnyClient())->name('api/client/user');
	Route::delete('client', 'ClientController@destroyCurrent')->middleware(Scopes::matchAnyUser())->name('api/client/delete');
	Route::delete('client/users', 'ClientController@destroyAll')->middleware(Scopes::matchAnyClient())->name('api/client/users/delete');
	Route::delete('client/{user_id}', 'ClientController@destroy')->middleware(Scopes::matchAnyClient())->name('api/client/user/delete');

	// Ressources
	/*
		index : /{ressource} en GET
		store : /{ressource} en POST
		show : /{ressource}/{id} en GET
		update : /{ressource}/{id} en PUT
		destroy : /{ressource}/{id} en DELETE
	*/

	Route::apiResources([
		'users'										=> 'UserController',
		'users/{user_id}/auths'						=> 'UserAuthController',
		'users/{user_id}/roles'						=> 'UserRoleController',
		'users/{user_id}/details'					=> 'UserDetailController',
		'users/{user_id}/preferences'				=> 'UserPreferenceController',

		// Routes `user` identiques à `users/{\Auth::id()}`
		'user/auths'								=> 'UserAuthController',
		'user/roles'								=> 'UserRoleController',
		'user/details'								=> 'UserDetailController',
		'user/preferences'							=> 'UserPreferenceController',
	]);

	Route::apiResources([
		'{resource_type}/{resource_id}/contacts'	=> 'ContactController',
		'groups/{group_id}/members'					=> 'GroupMemberController',
		'groups'									=> 'GroupController',
		'assos'										=> 'AssoController',
		'assos/{asso_id}/members'					=> 'AssoMemberController',
		'roles'										=> 'RoleController',
		'rooms'										=> 'RoomController',
		'partners'									=> 'PartnerController',
		'articles'									=> 'ArticleController',
		'events'									=> 'EventController',
		'visibilities'								=> 'VisibilityController',
  ]);
});
