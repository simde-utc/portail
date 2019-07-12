<?php
/**
 * Ginger service - Contribution manager.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class GingerServiceProvider extends ServiceProvider
{
    /**
     * Enregistre le service Ginger.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Ginger', function() {
            return new \App\Services\Ginger;
        });
    }
}
