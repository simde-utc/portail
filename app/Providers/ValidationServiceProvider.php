<?php
/**
 * Service Validation - Request validation manager.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Saves the validation services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Validation', function() {
            return new \App\Services\Validation;
        });
    }
}
