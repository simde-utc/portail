<?php

namespace App\Http\Controllers\v1\Visibility;

use App\Http\Controllers\v1\Controller;
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
	public function __construct() {
		$this->middleware(
			\Scopes::allowPublic()->matchAnyUserOrClient()
		);
	}

	/**
	 * List Visibilities
	 * @return JsonResponse
	 */

	public function index(): JsonResponse {
		$visibilities = Visibility::getSelection()->map(function ($visibility) {
			return $visibility->hideData();
		});

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
			return response()->json($visibility->hideSubData(), 200);
		else
			abort(500, 'Impossible de créer la visibilité');
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
			return response()->json($visibility->hideSubData(), 200);
		else
			abort(404, 'Visibilité non trouvée');
	}

	/**
	 * Update Visibility
	 *
	 * @param VisibilityRequest $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(VisibilityRequest $request, string $id): JsonResponse {
		$visibility = Visibility::find($id);

		if ($visibility) {
			if ($visibility->update($request->input()))
				return response()->json($visibility->hideSubData(), 201);
			else
				abort(500, 'Impossible de modifier la visibilité');
		}
		else
			abort(404, 'Visibilité non trouvée');
	}

	/**
	 * Delete Visibility
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(string $id): JsonResponse {
		$visibility = Visibility::find($id);

		if ($visibility) {
			if ($visibility->delete())
				abort(204);
			else
				abort(500, 'Impossible de supprimer la visibilité');
		}
		else
			abort(404, 'Visibilité non trouvée');
	}
}
