<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    public function boot() {}

    public function register() {
        $this->app->bind('Validation', function() {
            return new \App\Services\Validation;
        });
    }
}
