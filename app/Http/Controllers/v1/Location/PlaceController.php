<?php

namespace App\Http\Controllers\v1\Location;

use App\Http\Controllers\v1\Controller;
use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @resource Place
 *
 * Gestion des emplacements
 */
class PlaceController extends Controller
{
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
			return response()->json(['message' => 'Impossible de créer le lieu'], 500);

	}

	/**
	 * Show Place
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, int $id): JsonResponse {
		if (\Scopes::has($request, 'client-get-locations'))
			$place = Place::with('locations')->find($id);
		else
			$place = Place::find($id);

		if ($place)
			return response()->json($place, 200);
		else
			return response()->json(['message' => 'Impossible de trouver le lieu'], 500);
	}

	/**
	 * Update Place
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, int $id): JsonResponse {
		if (\Scopes::has($request, 'client-get-locations'))
			$place = Place::with('locations')->find($id);
		else
			$place = Place::find($id);

		if ($place) {
			if ($place->update($request->input()))
				return response()->json($place, 201);
			else
				return response()->json(['message' => 'Impossible d\'actualiser le lieu'], 500);
		}
		else
			return response()->json(['message' => 'Impossible de trouver le lieu'], 500);
	}

	/**
	 * Delete Place
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(int $id): JsonResponse {
		$place = Place::find($id);

		if ($place) {
			if ($place->softDelete())
				return response()->json(['message' => 'Le lieu a bien été supprimé'], 200);
			else
				return response()->json(['message' => 'Erreur lors de la suppression de le lieu'], 500);
		}
		else
			return response()->json(['message' => 'Impossible de trouver le lieu'], 500);
	}
}
