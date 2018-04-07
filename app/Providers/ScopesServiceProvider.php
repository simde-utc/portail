<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ScopesServiceProvider extends ServiceProvider
{
    public function boot() {}

    public function register() {
        $this->app->bind('Scopes', function() {
            return new \App\Services\Scopes;
        });
    }
}
