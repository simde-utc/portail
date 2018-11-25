<?php
/**
 * Service des routes.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Namespace où les controlleurs sont tous définis par défaut.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Définition des routes.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Définition des routes de l'applications.
     *
     * @return void
     */
    public function map()
    {
        $this->mapPassportRoutes();

        $this->mapApiRoutes();

        // A définir en dernier car récupère les HTTP 404.
        $this->mapWebRoutes();
    }

    /**
     * Définition des routes Passport.
     *
     * @return void
     */
    protected function mapPassportRoutes()
    {
        Passport::routes();

        Route::prefix('oauth')
        ->group(base_path('routes/oauth.php'));
    }

    /**
     * Définition des routes Web.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        $services = config('auth.services');

        foreach ($services as $provider => $data) {
            $file = base_path('routes/auth/'.$provider.'.php');

            if (file_exists($file)) {
                Route::middleware('web')
                    ->namespace($this->namespace)->prefix('login/'.$provider)->group($file);
            }
        }

        // A définir en dernier car la route '/' override tout.
        Route::middleware('web')
            ->namespace($this->namespace)->group(base_path('routes/web.php'));
    }

    /**
     * Définition des routes Api.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
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

                if ($i < $indexVersion) {
                    $middlewares[] = 'deprecatedVersion:'.$version;
                } else if ($i > $indexVersion || $indexVersion === false) {
                    $middlewares[] = 'betaVersion:'.$version;
                }

                Route::prefix('api/'.$version)
                    ->namespace($this->namespace.'\\'.$version)
                    ->middleware($middlewares)
                    ->group($file);
            }

            Route::any('api/'.$version.'/{whatever}', $this->namespace.'\RouteController@notFound')
                ->where('whatever', '.*');
        }

        Route::any('api/{whatever}', $this->namespace.'\RouteController@versionNotFound')
            ->where('whatever', '.*');
    }
}
