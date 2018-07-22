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

Route::prefix('v1')->namespace('v1')->group(function () {
	// Connexions
	Route::get('login', 'Client\LoginController@index')->middleware('guest')->name('api/login');
	Route::get('logout', 'Client\LoginController@destroy')->middleware(Scopes::matchAnyUser())->name('api/logout');

	// Informations relatives à l'utlisateur
	Route::get('user', 'User\UserController@show')->middleware(Scopes::matchAnyUser())->name('api/user');
	Route::patch('user', 'User\UserController@update')->middleware(Scopes::matchAnyUser())->name('api/user/update');

	// Gestions des authorisations données au client
	Route::get('client', 'Client\ClientController@index')->middleware(Scopes::matchAnyUserOrClient())->name('api/client');
	Route::get('client/users', 'Client\ClientController@getUsersClient')->middleware(Scopes::matchAnyClient())->name('api/client/users');
	Route::get('client/{user_id}', 'Client\ClientController@getUserClient')->middleware(Scopes::matchAnyClient())->name('api/client/user');
	Route::delete('client', 'Client\ClientController@destroyCurrent')->middleware(Scopes::matchAnyUser())->name('api/client/delete');
	Route::delete('client/users', 'Client\ClientController@destroyAll')->middleware(Scopes::matchAnyClient())->name('api/client/users/delete');
	Route::delete('client/{user_id}', 'Client\ClientController@destroy')->middleware(Scopes::matchAnyClient())->name('api/client/user/delete');

	// Ressources
	/*
		index : /{ressource} en GET
		store : /{ressource} en POST
		show : /{ressource}/{id} en GET
		update : /{ressource}/{id} en PUT
		destroy : /{ressource}/{id} en DELETE
	*/

	Route::apiResources([
		'users'										=> 'User\UserController',
		'users/{user_id}/auths'						=> 'User\AuthController',
		'users/{user_id}/roles'						=> 'User\RoleController',
		'users/{user_id}/details'					=> 'User\DetailController',
		'users/{user_id}/preferences'				=> 'User\PreferenceController',
		'users/{user_id}/calendars'					=> 'User\CalendarController',
		'users/{user_id}/assos'						=> 'User\AssoController',

		// Routes `user` identiques à `users/{\Auth::id()}`
		'user/auths'								=> 'User\AuthController',
		'user/roles'								=> 'User\RoleController',
		'user/details'								=> 'User\DetailController',
		'user/preferences'							=> 'User\PreferenceController',
		'user/calendars'							=> 'User\CalendarController',
		'user/contacts'								=> 'Contact\ContactController',
		'user/assos'								=> 'User\AssoController',
	]);

	Route::apiResources([
		'{resource_type}/{resource_id}/contacts'	=> 'Contact\ContactController',
		'groups/{group_id}/members'					=> 'Group\MemberController',
		'groups'									=> 'Group\GroupController',
		'assos'										=> 'Asso\AssoController',
		'assos/{asso_id}/members'					=> 'Asso\MemberController',
		'roles'										=> 'Role\RoleController',
		'places'									=> 'Location\PlaceController',
		'locations'									=> 'Location\LocationController',
		'rooms'										=> 'Location\RoomController',
		'events'									=> 'Event\EventController',
		'calendars'									=> 'Calendar\CalendarController',
		'calendars/{calendar_id}/events'			=> 'Calendar\EventController',
		'partners'									=> 'Partner\PartnerController',
		'articles'									=> 'Article\ArticleController',
		'visibilities'								=> 'Article\VisibilityController',
  ]);
});
