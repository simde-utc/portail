<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RouteController extends Controller
{
	public function index(Request $request, string $version) {
		$versions = config('portail.versions');
        $actualVersion = config('portail.version');
        $indexVersion = array_search($actualVersion, $versions);

		if ($i = array_search($version, $versions) >= 0) {
            $file = base_path('routes/api/'.$version.'.php');

            if (file_exists($file)) {
                $middlewares = [
                    'forceJson'
                ];

                if ($i < $indexVersion)
                    $middlewares[] = 'deprecatedVersion:'.$version;
                else if ($i > $indexVersion || $indexVersion === false)
                    $middlewares[] = 'betaVersion:'.$version;

                $routes = [];

                foreach (Route::getRoutes() as $route) {
                    if (($route->action['prefix'] ?? '') === 'api/'.$version) {
						$uri = str_replace('api/'.$version.'/', '', $route->uri);

						if (isset($routes[$uri]))
							$routes[$uri]['methods'][] = $route->methods[0];
						else {
							$routes[$uri] = [
	                            'url' => url($route->uri),
	                            'methods' => [
									$route->methods[0]
								],
	                        ];
						}
                    }
                }

                $data = [
                    'info' => 'Définition des routes api pour la '.$version,
                ];

                if ($i < $indexVersion)
                    $data['deprecated'] = true;
                else if ($i > $indexVersion || $indexVersion === false)
                    $data['beta'] = true;

				$data['routes'] = $routes;

                return response()->json($data);
            }
        }

		return abort(404, 'Version non existante');
	}
}
