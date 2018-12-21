<?php
/**
 * Gestion des routes admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

Route::get('/', 'HomeController@index');

Route::get('dashboard', 'DashboardController@index')->name('dashboard-index');

Route::get('auth/login', 'AuthController@getLogin');
Route::post('auth/login', 'AuthController@postLogin');
Route::get('auth/logout', 'AuthController@getLogout');

Route::get('charts', 'ChartsController@index')->name('charts-index');

Route::get('search', 'SearchController@index')->name('search-index');
Route::post('search', 'SearchController@search')->name('search-search');
Route::get('search/{user_id}', 'SearchController@show')->name('search-show');

Route::get('resources/users', 'Resource\\UserController@index')->name('users-index');
Route::get('resources/users/{user_id}', 'Resource\\UserController@show')->name('users-show');
Route::post('resources/users/{user_id}/impersonate', 'Resource\\UserController@impersonate')->name('users-impersonate');
Route::post('resources/users/{user_id}/contributeBde', 'Resource\\UserController@contributeBde')->name('users-contributeBde');

Route::resource('resources/access', 'Resource\\AccessController');
Route::resource('resources/articles', 'Resource\\ArticleController');
Route::resource('resources/article-actions', 'Resource\\ArticleActionController');
Route::resource('resources/assos', 'Resource\\AssoController');
Route::resource('resources/asso-access', 'Resource\\AssoAccessController');
Route::resource('resources/asso-types', 'Resource\\AssoTypeController');
Route::resource('resources/auth-apps', 'Resource\\AuthAppController');
Route::resource('resources/auth-cas', 'Resource\\AuthCasController');
Route::resource('resources/auth-passwords', 'Resource\\AuthPasswordController');
Route::resource('resources/calendars', 'Resource\\CalendarController');
Route::resource('resources/clients', 'Resource\\ClientController');
Route::resource('resources/comments', 'Resource\\CommentController');
Route::resource('resources/contacts', 'Resource\\ContactController');
Route::resource('resources/contact-types', 'Resource\\ContactTypeController');
Route::resource('resources/events', 'Resource\\EventController');
Route::resource('resources/event-details', 'Resource\\EventDetailController');
Route::resource('resources/groups', 'Resource\\GroupController');
Route::resource('resources/locations', 'Resource\\LocationController');
Route::resource('resources/notifications', 'Resource\\NotificationController');
Route::resource('resources/partners', 'Resource\\PartnerController');
Route::resource('resources/permissions', 'Resource\\PermissionController');
Route::resource('resources/places', 'Resource\\PlaceController');
Route::resource('resources/reservations', 'Resource\\ReservationController');
Route::resource('resources/reservation-types', 'Resource\\ReservationTypeController');
Route::resource('resources/roles', 'Resource\\RoleController');
Route::resource('resources/rooms', 'Resource\\RoomController');
Route::resource('resources/semesters', 'Resource\\SemesterController');
Route::resource('resources/services', 'Resource\\ServiceController');
Route::resource('resources/sessions', 'Resource\\SessionController');
Route::resource('resources/tags', 'Resource\\TagController');
Route::resource('resources/users', 'Resource\\UserController');
Route::resource('resources/user-details', 'Resource\\UserDetailController');
Route::resource('resources/user-preferences', 'Resource\\UserPreferenceController');
Route::resource('resources/visibilities', 'Resource\\VisibilityController');
