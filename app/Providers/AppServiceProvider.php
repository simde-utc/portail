<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Schema::defaultStringLength(191);       // Pour que 'email' puisse être une clé
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }
}
