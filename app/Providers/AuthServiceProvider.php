<?php
/**
 * Service de l'authentification.
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

    // Important d'être non différé car sinon les scopes ne sont pas chargés.
    protected $defer = false;

    /**
     * Enregistre les services d'authentification.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->passport();
    }

    /**
     * Enregistrement des actions pour les services d'authentification.
     *
     * @return void
     */
    public function register()
    {
        Passport::ignoreMigrations();
    }

    /**
     * Enregistrement du service Passport.
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
     * Liste tous les services différés
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
