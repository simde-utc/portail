<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Http\Requests\RoomRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::get();
        return response()->json($rooms, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoomRequest $request)
    {
        /*$room = Room::create($request->all());
        if($room)
        {
            $room->save();
            return response()->json($room, 200);
        }
        else
                return response()->json(["message" => "Impossible de créer la salle"], 500);*/

        $room = new Room;

        $room->name = $request->input('name');
        $room->asso_id = $request -> input('asso_id');

        if($room)
        {
            $room->save();
            return response()->json($room, 200);
        }
        else
                return response()->json(["message" => "Impossible de créer la salle"], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $room = Room::find($id);

        if($room)
            return response()->json($room, 200);
        else
            return response()->json(["message" => "Impossible de trouver la salle"], 500);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoomRequest $request, $id)
    {
        $room = Room::find($id);

        if($room)
        {
            //$room = Room::update($request->input());

            /*$room->name = $request->input('name');
            $room->asso_id = $request->input('asso_id');

            $romm->save();

            $room->update($request->al());
*/
        

            return response()->json($room, 200);
        }
        else
            return reponse()->json(["message" => "Impossible de trouver la salle"], 500);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $room = Room::find($id);

        if ($room)
        {
            $room->delete();
            return response()->json([], 200);
        }
        else
            return response()->json(["message" => "Impossible de trouver la salle"], 500);
    }
}
