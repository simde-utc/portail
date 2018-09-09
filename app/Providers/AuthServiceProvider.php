<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Models\Client;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The policy mappings for the application.
	 * @var array
	 */
	protected $policies = [
		'App\Model' => 'App\Policies\ModelPolicy',
	];

	// Important d'être non différé car sinon les scopes ne sont pas chargés
	protected $defer = false;

	/**
	 * Register any authentication / authorization services.
	 */
	public function boot()
	{
		$this->registerPolicies();

		$this->passport();
	}

	public function register() {
		Passport::ignoreMigrations();
	}

	public function passport() {
        Passport::useClientModel(Client::class);

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
