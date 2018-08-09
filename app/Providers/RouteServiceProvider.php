<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

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
    public function boot() {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map() {
		$this->mapPassportRoutes();

        $this->mapApiRoutes();

        // A définir en dernier
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapPassportRoutes() {
		Passport::routes();
        
		Route::prefix('oauth')
			->group(base_path('routes/oauth.php'));
    }


    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes() {
        $services = config('auth.services');

		foreach ($services as $provider => $data) {
            $file = base_path('routes/auth/'.$provider.'.php');

            if (file_exists($file)) {
                Route::middleware('web')
                    ->namespace($this->namespace)->prefix('login/'.$provider)->group($file);
            }
        }

        // A définir en dernier car la route '/' override tout
        Route::middleware('web')
            ->namespace($this->namespace)->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes() {
        $versions = config('portail.versions');
        $actualVersion = config('portail.version');
        $indexVersion = array_search($actualVersion, $versions);

        Route::prefix('api')
            ->middleware('forceJson')
            ->get('/{version}/', $this->namespace.'\RouteController@index');

		for ($i = 0; $i < count($versions); $i++) {
            $version = $versions[$i];
            $file = base_path('routes/api/'.$version.'.php');

            if (file_exists($file)) {
                $middlewares = [
                    'forceJson'
                ];

                if ($i < $indexVersion)
                    $middlewares[] = 'deprecatedVersion:'.$version;
                else if ($i > $indexVersion || $indexVersion === false)
                    $middlewares[] = 'betaVersion:'.$version;

                Route::prefix('api/'.$version)
                    ->namespace($this->namespace.'\\'.$version)
                    ->middleware($middlewares)
                    ->group($file);
            }
        }
    }
}
