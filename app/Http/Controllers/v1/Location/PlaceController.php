<?php

namespace App\Http\Controllers\v1\Location;

use App\Http\Controllers\v1\Controller;
use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\Controller\v1\HasPlaces;

/**
 * @resource Place
 *
 * Gestion des emplacements
 */
class PlaceController extends Controller
{
    use HasPlaces;

    public function __construct() {
		$this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-get-locations-places'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-create-locations-places'),
			['only' => ['store']]
		);
		$this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-set-locations-places'),
			['only' => ['update']]
		);
		$this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-manage-locations-places'),
			['only' => ['destroy']]
		);
    }

	/**
	 * List Places
	 *
	 * @return JsonResponse
	 */
	public function index(): JsonResponse {
		return response()->json(Place::get(), 200);
	}

	/**
	 * Create Place
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request): JsonResponse {
		$place = Place::create($request->all());

		if ($place)
			return response()->json($place, 200);
		else
			abort(500, 'Impossible de créer le lieu');

	}

	/**
	 * Show Place
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, int $id): JsonResponse {
        $place = $this->getPlace($request, $id);

		return response()->json($place, 200);
	}

	/**
	 * Update Place
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, int $id): JsonResponse {
        $place = $this->getPlace($request, $id);

		if ($place->update($request->input()))
			return response()->json($place, 201);
		else
            abort(500, 'Impossible d\'actualiser le lieu');
	}

	/**
	 * Delete Place
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(int $id): JsonResponse {
        $place = $this->getPlace($request, $id);

		if ($place->softDelete())
            abort(204);
		else
			abort(500, 'Erreur lors de la suppression de le lieu');
	}
}
