<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Http\Requests\RoomRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @resource Room
 *
 * Gestion des salles
 */
class RoomController extends Controller
{
	/**
	 * List Rooms
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$rooms = Room::get();
		return response()->json($rooms, 200);
	}

	/**
	 * Create Room
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(RoomRequest $request)
	{
		$room = Room::create($request->all());

		if ($room)
			return response()->json($room, 200);
		else
			return response()->json(['message' => 'Impossible de créer la salle'], 500);

	}

	/**
	 * Show Room
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$room = Room::find($id);

		if ($room)
			return response()->json($room, 200);
		else
			return response()->json(['message' => 'Impossible de trouver la salle'], 500);
	}

	/**
	 * Update Room
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$room = Room::find($id);
		if ($room) {
			if ($room->update($request->input()))
				return response()->json($room, 201);
			return response()->json(['message'=>'An error ocured'],500);
		}
		return response()->json(['message' => 'Impossible de trouver la salle'], 500);
	}

	/**
	 * Delete Room
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$room = Room::find($id);

		if ($room) {
			if ($room->delete())
				return response()->json(['message'=>'La salle a bien été supprimée'],200);
			return response()->json(['message'=>'Erreur lors de la suppression de la salle'],500);
		}
		return response()->json(['message' => 'Impossible de trouver la salle'], 500);
	}
}
