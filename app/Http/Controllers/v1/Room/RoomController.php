<?php

namespace App\Http\Controllers\v1\Room;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasRooms;
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
	use HasRooms;

	public function __construct() {
		$this->middleware(
			\Scopes::matchOne('user-get-rooms', 'client-get-rooms'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-create-rooms', 'client-create-rooms'),
				['permission:room']
			),
			['only' => ['store']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-edit-rooms', 'client-edit-rooms'),
				['permission:room']
			),
			['only' => ['update']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-remove-rooms', 'client-remove-rooms'),
				['permission:room']
			),
			['only' => ['destroy']]
		);
	}

	/**
	 * List Visibilities
	 * @return JsonResponse
	 */

	public function index(): JsonResponse {
		$rooms = Room::getSelection()->filter(function ($room) {
			return !\Auth::id() || $this->isVisibile($room, \Auth::id());
		})->values()->map(function ($room) {
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

		return response()->json($room->hideSubData(), 200);
	}

	/**
	 * Show Room
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show($id): JsonResponse {
		$room = $this->getRoom($id);

		return response()->json($room->hideSubData(), 200);
	}

	/**
	 * Update Room
	 *
	 * @param RoomRequest $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(RoomRequest $request, string $id): JsonResponse {
		$room = $this->getRoom($id);

		if ($room->update($request->input()))
			return response()->json($room->hideSubData(), 201);
		else
			abort(500, 'Impossible de modifier la salle');
	}

	/**
	 * Delete Room
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(string $id): JsonResponse {
		$room = $this->getRoom($id);

		if ($room->delete())
			abort(204);
		else
			abort(500, 'Impossible de supprimer la salle');
	}
}
