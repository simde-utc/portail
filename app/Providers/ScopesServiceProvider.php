<?php
/**
 * Service Scopes - Gestionnaire des scopes OAuth/Passport.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ScopesServiceProvider extends ServiceProvider
{
    /**
     * Enregistre le service Scopes.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Scopes', function() {
            return new \App\Services\Scopes;
        });
    }
}
