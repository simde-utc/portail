<?php

namespace App\Http\Controllers\v1\Room;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasRooms;
use App\Traits\Controller\v1\HasCreatorsAndOwners;
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
	use HasRooms, HasCreatorsAndOwners;

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
		$rooms = Room::getSelection()->filter(function ($room) use ($request) {
			return $this->tokenCanSee($request, $room, 'get') && (!\Auth::id() || $this->isVisibile($room, \Auth::id()));
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
		$inputs = $request->all();

		$owner = $this->getOwner($request, 'room', 'salle', 'create');
		$creator = $this->getCreatorFromOwner($request, $owner, 'room', 'salle', 'create');

		$inputs['created_by_id'] = $creator->id;
		$inputs['created_by_type'] = get_class($creator);
		$inputs['owned_by_id'] = $owner->id;
		$inputs['owned_by_type'] = get_class($owner);

		$room = Room::create($inputs);

		if ($room) {
			$room = $this->getRoom($request, \Auth::user(), $room->id);

			return response()->json($room->hideSubData(), 201);
		}
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
		$room = $this->getRoom($request, \Auth::user(), $id);

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
		$room = $this->getRoom($request, \Auth::user(), $id, 'edit');
		$inputs = $request->all();

		if ($request->filled('owned_by_type')) {
			$owner = $this->getOwner($request, 'room', 'salle', 'edit');

			$inputs['owned_by_id'] = $owner->id;
			$inputs['owned_by_type'] = get_class($owner);
		}

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
		$room = $this->getRoom($request, \Auth::user(), 'manage');

		if ($room->delete())
			abort(204);
		else
			abort(500, 'Impossible de supprimer la salle');
	}
}
