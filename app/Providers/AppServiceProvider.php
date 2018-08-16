<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		Schema::defaultStringLength(191);       // Pour que 'email' puisse être une clé

		$this->passport();
	}

	public function passport() {
		Passport::withoutCookieSerialization();
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		// ServiceProviders de développement
		if (!$this->app->environment('production')) {
		}

	}
}
