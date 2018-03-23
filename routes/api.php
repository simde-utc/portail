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
	Route::middleware(Scopes::matchOne('u-get-user'))->get('/onescope', function (Illuminate\Http\Request $request) {
	    return $request->user();
	});
	Route::middleware(Scopes::matchAll(['u-get-calendar-semester', 'u-get-user-assos']))->get('/allscopes', function (Illuminate\Http\Request $request) {
	    return $request->user();
	});
	Route::middleware(Scopes::matchOne(['c-get-users']))->get('/client', function ($request) {
	    return 'coucou';
	});

	/*
	// Authentication Routes
	Route::get('login', 'LoginController@index')->name('login');
	Route::get('login/{provider?}', 'LoginController@show')->name('login.show');
	Route::match(['get', 'post'], 'login/{provider}/process', 'LoginController@store')->name('login.process');
	Route::match(['get', 'post'], 'logout/{redirection?}', 'LoginController@destroy')->name('logout');
	*/
	// Route::apiResources([
	//     'photos' => 'PhotoControlle"r',
	//     'posts' => 'PostController'
	// ]);

	Route::apiResources([
	  'users'			=> 'UserController',
	  'assos' 		=> 'AssoController',
	  'assos/types' 	=> 'AssoTypeController',
	  'groups'        => 'GroupController',
	]);
});
