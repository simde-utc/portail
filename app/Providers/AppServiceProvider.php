<?php
/**
 * Application service.
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
     * Launched at each application start.
     *
     * @return void
     */
    public function boot()
    {
        // For 'email' to be a key.
        Schema::defaultStringLength(191);

        $this->passport();
    }

    /**
     * By-pass a patch to let Passport work.
     *
     * @return void
     */
    public function passport()
    {
        Passport::withoutCookieSerialization();
    }
}
