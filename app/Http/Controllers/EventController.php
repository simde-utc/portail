<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asso;
use App\Models\Event;
use App\Models\Calendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Visible\Visible;
use App\Interfaces\CanHaveEvents;
use App\Traits\HasVisibility;

/**
 * @resource Event
 *
 * Gestion des évènements
 */
class EventController extends Controller
{
	use HasVisibility;

	public function __construct() {
		$this->middleware(
			\Scopes::matchAnyUser()
		);
	}

	protected function hideEventData(Request $request, $event) {
		$event->created_by = $this->hideData($request, $event->created_by);
		$event->owned_by = $this->hideData($request, $event->owned_by);

		$event->makeHidden('visibility_id');

		return $event;
	}

	protected function getEvent(Request $request, int $id, bool $needRights = false) {
		$event = Event::with(['owned_by', 'created_by', 'visibility'])->find($id);

		if ($event) {
			if (!$this->isVisible($event))
				abort(403, 'Vous n\'avez pas le droit de consulter ce évènenement');

			if ($needRights && !$event->owned_by->isEventManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droit suffisant');

			return $event;
		}

		abort(404, 'Impossible de trouver le évènenement');
	}

	public function isPrivate($user_id, $model = null) {
		if ($model === null)
			return false;

		// Si c'est privée, uniquement les personnes ayant un calendrier contenant cet event peuvent le voir
		$user = User::find($user_id);
		$calendar_ids = $user->calendars->pluck('id')->merge($user->followedCalendars->pluck('id'));
		$event_calendar_ids = $model->calendars->pluck('id');

		$model->makeHidden('calendars');

		if (count($calendar_ids->intersect($event_calendar_ids)) !== 0)
			return true;

		return $model->owned_by->isEventAccessibleBy($user_id);
    }

	/**
	 * List Events
	 *
	 * @return JsonResponse
	 */
	public function index(Request $request): JsonResponse {
		$events = Event::with(['owned_by', 'created_by', 'visibility'])->get()->map(function ($event) use ($request) {
			return $this->hideEventData($request, $event);
		});

		return response()->json($events, 200);
	}

	/**
	 * Create Event
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request): JsonResponse {
		$inputs = $request->all();

		if (\Scopes::isUserToken($request))
			$creater = \Auth::user();
		else {
			// A faire au choix
		}

		$owner = \Auth::id();

		if (!($owner instanceof CanHaveEvents))
			abort(400, 'Le owner doit au moins pouvoir avoir un évènenement');
		if (!($owner instanceof CanHaveEvents))
			abort(400, 'Le owner doit au moins pouvoir avoir un évènenement');

		$inputs['created_by_id'] = $creater->id;
		$inputs['created_by_type'] = get_class($creater);
		$inputs['owned_by_id'] = $owner->id;
		$inputs['owned_by_type'] = get_class($owner);
		
		$calendar = Calendar::find($inputs['calendar_id']);

		if (!$calendar->owned_by->isCalendarManageableBy(\Auth::id()))
			abort(403, 'Vous n\'avez pas les droits suffisants pour ajouter cet évènenement à ce calendrier');

		$event = Event::create($inputs);

		if ($event) {
			$event->calendars()->assign($calendar);

			return response()->json($event, 201);
		}
		else
			return response()->json(['message' => 'Impossible de créer le évènenement'], 500);

	}

	/**
	 * Show Event
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, int $id): JsonResponse {
		$event = $this->getEvent($request, $id);
		$event = $this->hideEventData($request, $event);

		return response()->json($event, 200);
	}

	/**
	 * Update Event
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, $id): JsonResponse {
		$event = $this->getEvent($request, $id, true);

		if ($event->update($request->input()))
			return response()->json($this->hideEventData($event), 200);
		else
			abort(500, 'Impossible de modifier le évènenement');
	}

	/**
	 * Delete Event
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy($id): JsonResponse {
		$event = $this->getEvent($request, $id, true);
		$event->softDelete();

		abort(204);
	}
}
