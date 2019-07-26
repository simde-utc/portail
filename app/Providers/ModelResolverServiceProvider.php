<?php
/**
 * ModelResolver Service - Retrieves the models.
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
     * Save the ModelResolver service.
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
