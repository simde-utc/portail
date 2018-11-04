<?php
/**
 * Service de l'application.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Lancé à chaque démarrage de l'application.
     *
     * @return void
     */
    public function boot()
    {
        // Pour que 'email' puisse être une clé.
        Schema::defaultStringLength(191);

        $this->passport();
    }

    /**
     * Contourne un correctif pour laisser Passport fonctionner.
     *
     * @return void
     */
    public function passport()
    {
        Passport::withoutCookieSerialization();
    }

    /**
     * Enregistre tous les services de dévelopmment de l'application.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->app->environment('production')) {
        }
    }
}
