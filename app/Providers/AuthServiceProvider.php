<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The policy mappings for the application.
	 * @var array
	 */
	protected $policies = [
		'App\Model' => 'App\Policies\ModelPolicy',
	];

	protected $defer = true;

	/**
	 * Register any authentication / authorization services.
	 */
	public function boot()
	{
		$this->registerPolicies();

		// Singletonne tous les services d'authentification perso répertoriés dans auth.services
		foreach (config('auth.services') as $name => $config) {
			$this->app->singleton($name, function ($app) {
				return new $config['class']();
			});
		}

		$this->passport();
	}

	public function passport() {
		Passport::tokensCan(\Scopes::all());

	    Passport::tokensExpireIn(now()->addDays(15));

	    Passport::refreshTokensExpireIn(now()->addDays(30));
	}

	/**
	 * List all deferred services
	 * @return array dynamically all custom auth classes
	 */
	public function provides() {
		$classes = [];
		foreach (config('auth.services') as $service)
			array_push($classes, $service['class']);
		return $classes;
	}


}
