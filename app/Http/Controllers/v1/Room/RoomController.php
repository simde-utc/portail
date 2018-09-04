<?php

namespace App\Http\Controllers\v1\Room;

use App\Http\Controllers\v1\Controller;
use App\Http\Requests\RoomRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Room;


/**
 * @resource Room
 *
 * Gestion des salles
 */
class RoomController extends Controller
{
	public function __construct() {
		$this->middleware(array_merge(
			['user:contributorBde'],
			\Scopes::allowPublic()->matchAnyUserOrClient()
		));
	}

	/**
	 * List Visibilities
	 * @return JsonResponse
	 */

	public function index(): JsonResponse {
		$rooms = Room::getSelection()->map(function ($room) {
			return $room->hideData();
		});

		return response()->json($rooms, 200);
	}

	/**
	 * Create Room
	 *
	 * @param RoomRequest $request
	 * @return JsonResponse
	 */
	public function store(RoomRequest $request): JsonResponse {
		$room = Room::create($request->all());

		if ($room)
			return response()->json($room->hideSubData(), 200);
		else
			abort(500, 'Impossible de créer la salle');
	}

	/**
	 * Show Room
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show($id): JsonResponse {
		$room = Room::find($id);

		if ($room)
			return response()->json($room->hideSubData(), 200);
		else
			abort(404, 'Salle non trouvée');
	}

	/**
	 * Update Room
	 *
	 * @param RoomRequest $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(RoomRequest $request, string $id): JsonResponse {
		$room = Room::find($id);

		if ($room) {
			if ($room->update($request->input()))
				return response()->json($room->hideSubData(), 201);
			else
				abort(500, 'Impossible de modifier la salle');
		}
		else
			abort(404, 'Salle non trouvée');
	}

	/**
	 * Delete Room
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(string $id): JsonResponse {
		$room = Room::find($id);

		if ($room) {
			if ($Room->delete())
				abort(204);
			else
				abort(500, 'Impossible de supprimer la salle');
		}
		else
			abort(404, 'Salle non trouvée');
	}
}
