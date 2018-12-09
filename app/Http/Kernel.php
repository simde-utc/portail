<?php
/**
 * Interconnecte l'ensemble des middlewares et des controlleurs
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natan.danous@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Laravel\Passport\Http\Middleware\CreateFreshApiToken::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],

        'admin-portail' => [
            'auth:web',
            'admin.auth',
            'admin.pjax',
            'admin.bootstrap',
            'admin.check',
        ],
    ];

    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.any' => \App\Http\Middleware\CheckAny::class,
        'auth.user' => \App\Http\Middleware\CheckUser::class,
        'auth.client' => \App\Http\Middleware\CheckClient::class,
        'auth.check' => \App\Http\Middleware\CheckAuth::class,
        'auth.public' => \App\Http\Middleware\CheckPublic::class,
        'auth.public.any' => \App\Http\Middleware\CheckPublicAny::class,
        'auth.public.user' => \App\Http\Middleware\CheckPublicUser::class,
        'auth.public.client' => \App\Http\Middleware\CheckPublicClient::class,
        'auth.public.check' => \App\Http\Middleware\CheckPublicAuth::class,
        'admin.check' => \App\Admin\Middlewares\CheckAdmin::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'user' => \App\Http\Middleware\UserIs::class,
        'role' => \App\Http\Middleware\UserHasRole::class,
        'permission' => \App\Http\Middleware\UserHasPermission::class,
        'checkPassport' => \App\Http\Middleware\CheckPassport::class,
        'forceJson' => \App\Http\Middleware\ForceJson::class,
        'deprecatedVersion' => \App\Http\Middleware\DeprecatedVersion::class,
        'betaVersion' => \App\Http\Middleware\BetaVersion::class,
    ];
}
