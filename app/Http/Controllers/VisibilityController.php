<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisibilityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Visibility;


/**
 * @resource Visibility
 *
 * Gestion des visibilités
 */
class VisibilityController extends Controller
{
	/**
	 * List Visibilities
	 * @return JsonResponse
	 */

	public function index(): JsonResponse {
		$visibilities = Visibility::get();

		return response()->json($visibilities, 200);
	}

	/**
	 * Create Visibility
	 *
	 * @param VisibilityRequest $request
	 * @return JsonResponse
	 */
	public function store(VisibilityRequest $request): JsonResponse {
		$visibility = Visibility::create($request->all());

		if ($visibility)
			return response()->json($visibility, 200);
		else
			return response()->json(['message' => 'Impossible de créer la visibilité'], 500);

	}

	/**
	 * Show Visibility
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show($id): JsonResponse {
		$visibility = Visibility::find($id);

		if ($visibility)
			return response()->json($visibility, 200);
		else
			return response()->json(['message' => 'Impossible de trouver la visibilité'], 500);
	}

	/**
	 * Update Visibility
	 *
	 * @param VisibilityRequest $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(VisibilityRequest $request, $id): JsonResponse {
		$visibility = Visibility::find($id);

		if ($visibility) {
			if ($visibility->update($request->input()))
				return response()->json($visibility, 201);
			else
				return response()->json(['message' => 'Impossible de mettre à jour la visibilité'], 500);
		}
		else
			return response()->json(['message' => 'Impossible de trouver la  visibilité'], 500);
	}

	/**
	 * Delete Visibility
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy($id): JsonResponse {
		$visibility = Visibility::find($id);

		if ($visibility) {
			if ($visibility->delete())
				return response()->json(['message' => 'La visibilité a bien été supprimée'], 200);
			else
				return response()->json(['message' => 'Erreur lors de la suppression de la visibilité'], 500);
		}
		else
			return response()->json(['message' => 'Impossible de trouver la visibilité'], 500);
	}
}
