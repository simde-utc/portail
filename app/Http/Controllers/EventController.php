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
            return response()->json($event, 202);
        return response()->json(["message" => "Impossible de trouver l'évènement"], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EventRequest $request, $id)
    {
        $event = Event::find($id);
		if($event){
			$ok = $event->update($request);
			if($ok)
				return response()->json($article,201);
			return
		}
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
