<?php

namespace App\Http\Controllers\v1\Location;

use App\Http\Controllers\v1\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @resource Location
 *
 * Gestion des lieux
 */
class LocationController extends Controller
{
    public function __construct() {
		$this->middleware(
			\Scopes::matchOne(
				['client-get-locations']
			),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['client-create-locations']
			),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['client-set-locations']
			),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['client-manage-locations']
			),
			['only' => ['destroy']]
		);
    }

	/**
	 * List Locations
	 *
	 * @return JsonResponse
	 */
	public function index(): JsonResponse {
		return response()->json(Location::get(), 200);
	}

	/**
	 * Create Location
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request): JsonResponse {
		$location = Location::create($request->all());

		if ($location)
			return response()->json($location, 200);
		else
			return response()->json(['message' => 'Impossible de créer l\'emplacement'], 500);

	}

	/**
	 * Show Location
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(int $id): JsonResponse {
		$location = Location::with('place')->find($id);

		if ($location)
			return response()->json($location, 200);
		else
			return response()->json(['message' => 'Impossible de trouver l\'emplacement'], 500);
	}

	/**
	 * Update Location
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, int $id): JsonResponse {
		$location = Location::with('place')->find($id);

		if ($location) {
			if ($location->update($request->input()))
				return response()->json($location, 201);
			else
				return response()->json(['message' => 'Impossible d\'actualiser l\'emplacement'], 500);
		}
		else
			return response()->json(['message' => 'Impossible de trouver l\'emplacement'], 500);
	}

	/**
	 * Delete Location
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(int $id): JsonResponse {
		$location = Location::find($id);

		if ($location) {
			if ($location->softDelete())
				return response()->json(['message' => 'L\'emplacement a bien été supprimée'], 200);
			else
				return response()->json(['message' => 'Erreur lors de la suppression de l\'emplacement'], 500);
		}
		else
			return response()->json(['message' => 'Impossible de trouver l\'emplacement'], 500);
	}
}
