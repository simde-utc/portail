<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\EventRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Visible\Visible;


/**
 * @resource Event
 *
 * Gestion des évènements
 */
class EventController extends Controller
{
	/**
	 * List Events
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$events = Event::get();
		return response()->json($events, 200);
	}

	/**
	 * Create Event
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
				return response()->json(['message' => 'Impossible de créer l\'évènement'], 500);

	}

	/**
	 * Show Event
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$event = Event::find($id);

		if($event)
			return response()->json($event, 202);
		return response()->json(['message' => 'Impossible de trouver l\'évènement'], 404);
	}

	/**
	 * Update Event
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(EventRequest $request, $id)
	{
		$event = Event::find($id);
		if($event){
			$ok = $event->update($request->input());
			if($ok)
				return response()->json($event, 201);
			return response()->json(['message'=>'An error ocured'],500);
		}
	}

	/**
	 * Delete Event
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
			return response()->json(['message' => 'Impossible de trouver l\'évènement'], 500);
	}
}
