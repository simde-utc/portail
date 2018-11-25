<?php
/**
 * Service ModelResolver - Permet de retrouver les modèles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModelResolverServiceProvider extends ServiceProvider
{
    /**
     * Enregistre le service ModelResolver.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('ModelResolver', function() {
            return new \App\Services\ModelResolver;
        });
    }
}
