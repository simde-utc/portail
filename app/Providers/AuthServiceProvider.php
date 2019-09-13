<?php
/**
 * Authentification service.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Models\Client;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];


    // It is important to be not deferred because otherwise scopes are not loaded.
    protected $defer = false;

    /**
     * Save authentification services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->passport();
    }

    /**
     * Save actions for authentification services.
     *
     * @return void
     */
    public function register()
    {
        Passport::ignoreMigrations();
    }

    /**
     * Passport service save.
     *
     * @return void
     */
    public function passport()
    {
        Passport::useClientModel(Client::class);

        Passport::tokensCan(\Scopes::all());

        Passport::tokensExpireIn(now()->addDays(15));

        Passport::refreshTokensExpireIn(now()->addDays(30));
    }

    /**
     * List all differed services.
     *
     * @return array
     */
    public function provides()
    {
        $classes = [];

        foreach (config('auth.services') as $service) {
            array_push($classes, $service['class']);
        }

        return $classes;
    }
}
