<?php
/**
 * Service Scopes - OAuth/Passport scopes manager.
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
     * Saves the scope service.
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
