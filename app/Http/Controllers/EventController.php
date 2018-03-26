<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\EventRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Visible\Visible;


class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::get();
        return response()->json($events, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventRequest $request)
    {
        $event = Event::create($request->all());

        if($event)
        {
            return response()->json($event, 200);
        }
        else
                return response()->json(["message" => "Impossible de créer l'évènement"], 500);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::find($id);

        if($event)
            return response()->json($event, 200);
        else
            return response()->json(["message" => "Impossible de trouver l'évènement"], 500);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if($event)
        {
            //Le "update ne passe pas"
            //$event = Event::update($request->all());

            $room->title = $request->input('title');
            $room->description = $request->input('description');
            $room->image = $request->input('image')->nullable();
            $room->from = $request->input('from');
            $room->to = $request->input('to');
            $room->visibility_id = $request->input('visibility_id');
            $room->place = $request->input('place');

            $room->save();

            return response()->json($event, 200);
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
       $event = Event::find($id);

        if ($event)
        {
            $event->delete();
            return response()->json([], 200);
        }
        else
            return response()->json(["message" => "Impossible de trouver l'évènement"], 500);
    }
}
