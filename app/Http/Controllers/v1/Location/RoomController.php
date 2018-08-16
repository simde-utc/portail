<?php

namespace App\Http\Controllers\v1\Location;

use App\Http\Controllers\v1\Controller;
use App\Models\Room;
use App\Http\Requests\RoomRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @resource Room
 *
 * Gestion des salles
 */
class RoomController extends Controller
{
	// BIG TODO

	/**
	 * List Rooms
	 *
	 * @return JsonResponse
	 */
	public function index(): JsonResponse {
		$rooms = Room::get();

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
			return response()->json($room, 200);
		else
			return response()->json(['message' => 'Impossible de créer la salle'], 500);

	}

	/**
	 * Show Room
	 *
	 * @param  string $id
	 * @return JsonResponse
	 */
	public function show($id): JsonResponse {
		$room = Room::find($id);

		if ($room)
			return response()->json($room, 200);
		else
			return response()->json(['message' => 'Impossible de trouver la salle'], 500);
	}

	/**
	 * Update Room
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  string $id
	 * @return JsonResponse
	 */
	public function update(Request $request, $id): JsonResponse {
		$room = Room::find($id);
		if ($room) {
			if ($room->update($request->input()))
				return response()->json($room, 201);
			else
				return response()->json(['message' => 'An error ocured'], 500);
		}
		else
			return response()->json(['message' => 'Impossible de trouver la salle'], 500);
	}

	/**
	 * Delete Room
	 *
	 * @param  string $id
	 * @return JsonResponse
	 */
	public function destroy($id): JsonResponse {
		$room = Room::find($id);

		if ($room) {
			if ($room->delete())
				return response()->json(['message' => 'La salle a bien été supprimée'], 200);
			else
				return response()->json(['message' => 'Erreur lors de la suppression de la salle'], 500);
		}
		else
			return response()->json(['message' => 'Impossible de trouver la salle'], 500);
	}
}
