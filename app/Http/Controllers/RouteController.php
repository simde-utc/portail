<?php
/**
 * Génère les routes de l'api
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RouteController extends Controller
{
    /**
     * Définition des middlewares utilisés.
     */
    public function __construct()
    {
        $this->middleware('forceJson');
    }

    /**
     * Permet de générer la liste des routes pour une version précise
     * @param  string $version
     * @return array
     */
    public function generateRouteList(string $version)
    {
        $routes = [];

        foreach (Route::getRoutes() as $route) {
            if (substr($route->uri, 0, strlen('api/'.$version)) === 'api/'.$version) {
                $uri = str_replace('api/'.$version.'/', '', $route->uri);

                if (isset($routes[$uri])) {
                    $routes[$uri]['methods'][] = $route->methods[0];
                } else {
                    $routes[$uri] = [
                        'url' => url($route->uri),
                        'methods' => [
                            $route->methods[0]
                        ],
                    ];
                }
            }
        }

        return $routes;
    }

    /**
     * Liste toutes les routes api d'une version précise.
     * @param  Request $request
     * @param  string  $version
     * @return mixed
     */
    public function index(Request $request, string $version)
    {
        $versions = config('portail.versions');
        $actualVersion = config('portail.version');
        $indexVersion = array_search($actualVersion, $versions);

        if ($index = array_search($version, $versions) >= 0) {
            $file = base_path('routes/api/'.$version.'.php');

            if (file_exists($file)) {
                $middlewares = [
                    'forceJson'
                ];

                if ($index < $indexVersion) {
                    $middlewares[] = 'deprecatedVersion:'.$version;
                } else if ($index > $indexVersion || $indexVersion === false) {
                    $middlewares[] = 'betaVersion:'.$version;
                }

                $data = [
                    'info' => 'Définition des routes api pour la '.$version,
                ];

                if ($index < $indexVersion) {
                    $data['deprecated'] = true;
                } else if ($index > $indexVersion || $indexVersion === false) {
                    $data['beta'] = true;
                }

                $data['routes'] = $this->generateRouteList($version);

                return response()->json($data);
            }
        }

        return abort(404, 'Version non existante');
    }

    /**
     * Retour expliquant que la version n'existe pas
     * @param  Request $request
     * @param  string  $version
     * @return mixed
     */
    public function versionNotFound(Request $request, string $version)
    {
        abort(404, 'Version non existante');
    }

    /**
     * Retour expliquant que la route api n'existe pas
     * @return mixed
     */
    public function notFound()
    {
        abort(404, 'Route api non trouvée');
    }
}
