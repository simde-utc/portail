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

Route::middleware('auth:api')->get('/user', function (Illuminate\Http\Request $request) {
    return $request->user();
});

// Authentication Routes
Route::get('login', 'LoginController@index')->name('login');
Route::get('login/{provider?}', 'LoginController@show')->name('login.show');
Route::match(['get', 'post'], 'login/{provider}/process', 'LoginController@store')->name('login.process');
Route::match(['get', 'post'], 'logout/{redirection?}', 'LoginController@destroy')->name('logout');

// Route::apiResources([
//     'photos' => 'PhotoController',
//     'posts' => 'PostController'
// ]);

Route::apiResources([
  'users'			=> 'UserController',
  'assos' 		=> 'AssoController',
  'assos/types' 	=> 'AssoTypeController',
  'groups'        => 'GroupController',
]);
