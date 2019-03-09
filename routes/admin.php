<?php
/**
 * Gestion des routes admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */


/**
 * Routes pour l'authentification admin.
 */

Route::get('auth/login', 'AuthController@getLogin');
Route::post('auth/login', 'AuthController@postLogin');
Route::get('auth/logout', 'AuthController@getLogout');

/**
 * Routes classiques d'affichage admin.
 */

Route::get('/', 'HomeController@index');
Route::get('dashboard', 'DashboardController@index')->name('dashboard-index');
Route::get('charts', 'ChartsController@index')->name('charts-index');

/**
 * Routes de recherche.
 */

Route::get('search/user', 'SearchUserController@index')->name('search-user-index');
Route::post('search/user', 'SearchUserController@search')->name('search-user-search');
Route::get('search/user/{user_id}', 'SearchUserController@show')->name('search-user-show');

Route::get('search/contributor', 'SearchContributorController@index')->name('search-contributor-index');
Route::get('search/contributor/{login}', 'SearchContributorController@show')->name('search-contributor-show');

/**
 * Routes de gestion.
 */

Route::get('management/access', 'AccessController@index')->name('access-index');
Route::put('management/access/{access_id}', 'AccessController@store')->name('access-store');

Route::get('management/assos/members', 'AssoMemberController@index')->name('asso-member-index');
Route::post('management/assos/members/{asso_id}/{member_id}/{semester_id}', 'AssoMemberController@store')->name('asso-member-validate');
Route::put('management/assos/members/{asso_id}/{member_id}/{semester_id}', 'AssoMemberController@update')->name('asso-member-update');
Route::delete('management/assos/members/{asso_id}/{member_id}/{semester_id}', 'AssoMemberController@delete')->name('asso-member-delete');

Route::get('management/users/roles', 'Management\\UserRoleController@index')->name('user-role-index');
Route::post('management/users/roles', 'Management\\UserRoleController@store')->name('user-role-store');
Route::get('management/users/roles/create', 'Management\\UserRoleController@create')->name('user-role-create');
Route::put('management/users/roles/{user_id}/{semester_id}', 'Management\\UserRoleController@update')->name('user-role-update');
Route::delete('management/users/roles/{user_id}/{semester_id}', 'Management\\UserRoleController@delete')->name('user-role-delete');

Route::get('management/users/permissions', 'Management\\UserPermissionController@index')->name('user-permission-index');
Route::post('management/users/permissions', 'Management\\UserPermissionController@store')->name('user-permission-store');
Route::get('management/users/permissions/create', 'Management\\UserPermissionController@create')->name('user-permission-create');
Route::put('management/users/permissions/{user_id}/{semester_id}', 'Management\\UserPermissionController@update')->name('user-permission-update');
Route::delete('management/users/permissions/{user_id}/{semester_id}', 'Management\\UserPermissionController@delete')->name('user-permission-delete');

/**
 * Routes de gestion des ressources.
 */

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
Route::resource('resources/bookings', 'Resource\\BookingController');
Route::resource('resources/booking-types', 'Resource\\BookingTypeController');
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
