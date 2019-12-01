<?php
/**
 * Defines first version API routes.
 * /!\ All Routes are prefixed by 'api/v1'.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

/**
 * Connections.
 */

Route::get('login', 'Client\LoginController@index')->middleware('guest')->name('api/login');
Route::get('logout', 'Client\LoginController@destroy')->middleware(Scopes::matchAnyUser())->name('api/logout');


/*
 * User related information.
 */

Route::get('user', 'User\UserController@show')->middleware(Scopes::matchAnyUser())->name('api/user');
Route::patch('user', 'User\UserController@update')->middleware(Scopes::matchAnyUser())->name('api/user/update');
Route::get('user/types/description', 'User\UserController@getLocalizedTypes')->middleware(Scopes::matchAnyUser())->name('api/user/types/description');

/*
 * Management of client given authorization.
 */

Route::get('client', 'Client\ClientController@index')->middleware(Scopes::matchAnyUserOrClient())->name('api/client');
Route::get('client/users', 'Client\ClientController@getUsersClient')->middleware(Scopes::matchAnyClient())->name('api/client/users');
Route::get('client/{user_id}', 'Client\ClientController@getUserClient')->middleware(Scopes::matchAnyClient())->name('api/client/user');
Route::delete('client', 'Client\ClientController@destroyCurrent')->middleware(Scopes::matchAnyUser())->name('api/client/delete');
Route::delete('client/users', 'Client\ClientController@destroyAll')->middleware(Scopes::matchAnyClient())->name('api/client/users/delete');
Route::delete('client/{user_id}', 'Client\ClientController@destroy')->middleware(Scopes::matchAnyClient())->name('api/client/user/delete');




/*
 * Ressouces:
 *
 *  index:   /{ressource}      in GET
 *  store:   /{ressource}      in POST
 *  show:    /{ressource}/{id} in GET
 *  update:  /{ressource}/{id} in PUT
 *  destroy: /{ressource}/{id} in DELETE

 *  bulkShow:    /{ressource}/{id1,id2} in GET
 *  bulkStore:   /{ressource}/{id1,id2} in POST
 *  bulkUpdate:  /{ressource}/{id1,id2} in PUT
 *  bulkDestroy: /{ressource}/{id1,id2} in DELETE
 *

    /*
 * Routes only for clients/connected users.
 */

Route::group(['middleware' => 'user:active'], function () {
    /*
     * Routes defined for the user.
     */

    Route::apiBulkResources([
        'users'	=> 'User\UserController',
        'users/{user_id}/roles'	=> 'User\RoleController',
        'users/{user_id}/calendars' => 'User\CalendarController',
        'users/{user_id}/permissions' => 'Permission\AssignmentController',

        /*
         * Routes `user` identical to `users/{Auth::id()`}.
         */

        'user/roles' => 'User\RoleController',
        'user/calendars' => 'User\CalendarController',
        'user/contacts'	=> 'Contact\ContactController',
        'user/permissions' => 'Permission\AssignmentController',
    ]);

    /*
     * Routes for all resources.
     */

    Route::apiBulkResources([
        '{resource_type}/{resource_id}/contacts' => 'Contact\ContactController',
        '{resource_type}/{resource_id}/comments' => 'Comment\CommentController',
        '{resource_type}/{resource_id}/members/{user_id}/permissions' => 'Permission\AssignmentController',
        'groups/{group_id}/members'	=> 'Group\MemberController',
        'groups' => 'Group\GroupController',
        'assos/{asso_id}/members' => 'Asso\MemberController',
        'assos/{asso_id}/access' => 'Asso\AccessController',
        'roles' => 'Role\RoleController',
        'permissions' => 'Permission\PermissionController',
        'rooms' => 'Room\RoomController',
        'rooms/{room_id}/bookings' => 'Room\BookingController',
        'bookings/types' => 'Booking\BookingTypeController',
        'calendars/{calendar_id}/events' => 'Calendar\EventController',
        'faqs' => 'Faq\CategoryController',
        'faqs/{category_id}/questions' => 'Faq\FaqController',
    ]);
});



/*
 * Routes for all.
 */

Route::group([], function () {
    /*
     * Routes for user.
     */

    Route::apiBulkResources([
        'users/{user_id}/notifications' => 'User\NotificationController',
        'users/{user_id}/articles/{article_id}/actions'	=> 'User\Article\ActionController',
        'users/{user_id}/auths' => 'User\AuthController',
        'users/{user_id}/details' => 'User\DetailController',
        'users/{user_id}/preferences' => 'User\PreferenceController',
        'users/{user_id}/assos' => 'User\AssoController',
        'users/{user_id}/services' => 'User\ServiceController',
        'users/{user_id}/contributions' => 'User\ContributionController',

    /*
     * Routes `user` identical to `users/{Auth::id()}`.
     */

        'user/auths' => 'User\AuthController',
        'user/details' => 'User\DetailController',
        'user/preferences' => 'User\PreferenceController',
        'user/assos' => 'User\AssoController',
        'user/services' => 'User\ServiceController',
        'user/articles/{article_id}/actions' => 'User\Article\ActionController',
        'user/notifications' => 'User\NotificationController',
        'user/contributions' => 'User\ContributionController',
    ]);

    /*
     * Routes for all ressources.
     */

    Route::apiBulkResources([
        'access' => 'Access\AccessController',
        'assos' => 'Asso\AssoController',
        'services' => 'Service\ServiceController',
        'places' => 'Location\PlaceController',
        'locations' => 'Location\LocationController',
        'events' => 'Event\EventController',
        'calendars' => 'Calendar\CalendarController',
        'partners' => 'Partner\PartnerController',
        'articles' => 'Article\ArticleController',
        'articles/{article_id}/actions'	=> 'Article\ActionController',
        'visibilities' => 'Visibility\VisibilityController',
        'semesters' => 'Semester\SemesterController',
    ]);
});
