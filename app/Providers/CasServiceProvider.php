<?php

namespace App\Providers;

use App\Services\CAS;
use Illuminate\Support\ServiceProvider;

class CasServiceProvider extends ServiceProvider
{
	protected $defer = true;

	/**
	 * Bootstrap services.
	 */
	public function boot() {
		//
	}

	/**
	 * Register services.
	 */
	public function register() {
		$this->app->singleton(CAS::class, function ($app) {
			return new CAS();
		});
	}

	/**
	 * Get the services provided by the provider.
	 */
	public function provides() {
		return [
			CAS::class
		];
	}
}
