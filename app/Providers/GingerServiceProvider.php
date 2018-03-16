<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class GingerServiceProvider extends ServiceProvider
{
    public function boot() {}

    public function register() {
        $this->app->bind('Ginger', function() {
            return new \App\Services\Ginger;
        });
    }
}
