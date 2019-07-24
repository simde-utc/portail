<?php
/**
 * Manage rooms.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Room;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasRooms;
use App\Traits\Controller\v1\HasCreatorsAndOwners;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Http\Requests\RoomRequest;

class RoomController extends Controller
{
    use HasRooms, HasCreatorsAndOwners;

    /**
     * Must be able to manage rooms.
     */
    public function __construct()
    {
        $this->middleware(
	        \Scopes::matchOne('user-get-rooms', 'client-get-rooms'),
	        ['only' => ['all', 'get']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOne('user-create-rooms', 'client-create-rooms'),
		        ['permission:room']
	        ),
	        ['only' => ['create']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOne('user-edit-rooms', 'client-edit-rooms'),
		        ['permission:room']
	        ),
        	['only' => ['edit']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOne('user-remove-rooms', 'client-remove-rooms'),
		        ['permission:room']
	        ),
	        ['only' => ['remove']]
        );
    }

    /**
     * List rooms.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $rooms = Room::getSelection()->filter(function ($room) use ($request) {
            return $this->tokenCanSee($request, $room, 'get');
        });

        return response()->json($rooms->values()->map(function ($room) {
            return $room->hideData();
        }), 200);
    }

    /**
     * Create a room.
     *
     * @param  RoomRequest $request
     * @return JsonResponse
     */
    public function store(RoomRequest $request): JsonResponse
    {
        $inputs = $request->all();

        $owner = $this->getOwner($request, 'room', 'salle', 'create');
        $creator = $this->getCreatorFromOwner($request, $owner, 'room', 'salle', 'create');

        $inputs['created_by_id'] = $creator->id;
        $inputs['created_by_type'] = get_class($creator);
        $inputs['owned_by_id'] = $owner->id;
        $inputs['owned_by_type'] = get_class($owner);

        $room = Room::create($inputs);

        return response()->json($room->refresh()->hideSubData(), 201);
    }

    /**
     * Show a room.
     *
     * @param Request $request
     * @param string  $room_id
     * @return JsonResponse
     */
    public function show(Request $request, string $room_id): JsonResponse
    {
        $room = $this->getRoom($request, \Auth::user(), $room_id);

        return response()->json($room->hideSubData(), 200);
    }

    /**
     * Update a room.
     *
     * @param RoomRequest $request
     * @param string      $room_id
     * @return JsonResponse
     */
    public function update(RoomRequest $request, string $room_id): JsonResponse
    {
        $room = $this->getRoom($request, \Auth::user(), $room_id, 'edit');
        $inputs = $request->all();

        if ($request->filled('owned_by_type')) {
            $owner = $this->getOwner($request, 'room', 'salle', 'edit');

            $inputs['owned_by_id'] = $owner->id;
            $inputs['owned_by_type'] = get_class($owner);
        }

        if ($room->update($request->input())) {
            return response()->json($room->hideSubData(), 201);
        } else {
            abort(500, 'Impossible de modifier la salle');
        }
    }

    /**
     * Delete a room.
     *
     * @param Request $request
     * @param string  $room_id
     * @return void
     */
    public function destroy(Request $request, string $room_id)
    {
        $room = $this->getRoom($request, \Auth::user(), 'manage');

        if ($room->delete()) {
            abort(204);
        } else {
            abort(500, 'Impossible de supprimer la salle');
        }
    }
}
