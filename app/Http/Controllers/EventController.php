<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\EventRequest;
use Illuminate\Http\JsonResponse;
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
	 * @return JsonResponse
	 */
	public function index(): JsonResponse {
		$events = Event::get();

		return response()->json($events, 200);
	}

	/**
	 * Create Event
	 *
	 * @param EventRequest $request
	 * @return JsonResponse
	 */
	public function store(EventRequest $request): JsonResponse {
		$event = Event::create($request->all());

		if ($event)
			return response()->json($event, 200);
		else
			return response()->json(['message' => 'Impossible de créer l\'évènement'], 500);

	}

	/**
	 * Show Event
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show($id): JsonResponse {
		$event = Event::find($id);

		if ($event)
			return response()->json($event, 202);

		return response()->json(['message' => 'Impossible de trouver l\'évènement'], 404);
	}

	/**
	 * Update Event
	 *
	 * @param EventRequest $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(EventRequest $request, $id): JsonResponse {
		$event = Event::find($id);
		if ($event) {
			if ($event->update($request->input()))
				return response()->json($event, 201);
			else
				return response()->json(['message' => 'An error ocured'], 500);
		}
	}

	/**
	 * Delete Event
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy($id): JsonResponse {
		$event = Event::find($id);

		if ($event) {
			$event->delete();

			return response()->json([], 200);
		}
		else
			return response()->json(['message' => 'Impossible de trouver l\'évènement'], 500);
	}
}
