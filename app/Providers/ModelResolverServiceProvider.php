<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModelResolverServiceProvider extends ServiceProvider
{
    public function boot() {}

    public function register() {
        $this->app->bind('ModelResolver', function() {
            return new \App\Services\ModelResolver;
        });
    }
}
