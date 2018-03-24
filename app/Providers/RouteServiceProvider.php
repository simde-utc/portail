<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Facades\Scopes;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
		$this->mapPassportRoutes();

        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapPassportRoutes()
    {
		Passport::routes();

		Passport::tokensExpireIn(now()->addDays(15));

		Passport::refreshTokensExpireIn(now()->addDays(30));

		Passport::tokensCan(Scopes::all());

		// Routes modifiées
		Route::get('oauth/clients', '\App\Http\Controllers\Passport\ClientController@forUser')->middleware(['web', 'auth']);
		Route::post('oauth/clients', '\App\Http\Controllers\Passport\ClientController@store')->middleware(['web', 'auth', 'admin']);
		Route::put('oauth/clients/{client_id}', '\App\Http\Controllers\Passport\ClientController@update')->middleware(['web', 'auth', 'admin']);

		Route::get('oauth/authorize', '\Laravel\Passport\Http\Controllers\AuthorizationController@authorize')->middleware(['web', 'auth', 'checkPassport']);

		Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken')->middleware(['throttle', 'checkPassport']);
    }


    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
