<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asso;
use App\Models\Event;
use App\Models\Calendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use App\Services\Visible\Visible;
use App\Interfaces\CanHaveCalendars;
use App\Traits\HasVisibility;

/**
 * @resource CalendarEvent
 *
 * Gestion des évenements des calendriers
 */
class CalendarEventController extends Controller
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

		$event->makeHidden(['location_id', 'visibility_id']);

		if ($event->pivot)
			$event->pivot->makeHidden(['calendar_id', 'event_id']);

		return $event;
	}

	protected function getCalendar(Request $request, int $id, bool $needRights = false) {
		$calendar = Calendar::with('events')->find($id);

		if ($calendar) {
			if (!$this->isVisible($calendar))
				abort(403, 'Vous n\'avez pas le droit de consulter ce calendrier');

			if ($needRights && !$calendar->owned_by->isCalendarManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droits suffisants');

			return $calendar;
		}

		abort(404, 'Impossible de trouver le calendrier');
	}

	protected function getEvent(Request $request, int $id, bool $needRights = false) {
		$event = Event::with(['owned_by', 'created_by', 'visibility', 'details', 'location'])->find($id);

		if ($event) {
			if (!$this->isVisible($event))
				abort(403, 'Vous n\'avez pas le droit de consulter ce évènenement');

			if ($needRights && !$event->owned_by->isEventManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droit suffisant');

			return $event;
		}

		abort(404, 'Impossible de trouver le évènenement');
	}

	protected function getEventFromCalendar(Request $request, Calendar $calendar, int $id) {
		$event = $calendar->events()->with(['owned_by', 'created_by', 'visibility', 'details', 'location'])->find($id);

		if ($event) {
			if (!$this->isVisible($event))
				abort(403, 'Vous n\'avez pas le droit de consulter ce évènenement');

			return $event;
		}

		abort(404, 'L\'évènement n\'existe pas ou ne fait pas parti du calendrier');
	}

	public function isPrivate($user_id, $model = null) {
		if ($model === null)
			return false;

		// Si c'est privée, uniquement les followers et ceux qui possèdent le droit peuvent le voir
		if ($model instanceof Calendar) {
			if ($model->followers()->wherePivot('user_id', $user_id)->exists())
				return true;
		}
		else if ($model instanceof Event) {
			// Si c'est privée, uniquement les personnes ayant un calendrier contenant cet event peuvent le voir
			$user = User::find($user_id);
			$calendar_ids = $user->calendars->pluck('id')->merge($user->followedCalendars->pluck('id'));
			$event_calendar_ids = $model->calendars->pluck('id');

			$model->makeHidden('calendars');

			if (count($calendar_ids->intersect($event_calendar_ids)) !== 0)
				return true;
		}

		return $model->owned_by->isCalendarAccessibleBy($user_id);
    }

	/**
	 * List Calendars
	 *
	 * @return JsonResponse
	 */
	public function index(Request $request, int $calendar_id): JsonResponse {
		$calendar = $this->getCalendar($request, $calendar_id);
		$events = $calendar->events()->with(['visibility', 'location', 'created_by', 'owned_by'])->get()->map(function ($event) use ($request) {
			return $this->hideEventData($request, $event);
		});

		return response()->json($events, 200);
	}

	/**
	 * Create Calendar
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request, int $calendar_id): JsonResponse {
		$calendar = $this->getCalendar($request, $calendar_id, true);
		$user = \Auth::user();

		$events = [];

		if ($request->filled('event_ids')) {
			foreach ($request->input('event_ids') as $event_id) {
				$events[] = $this->getEvent($request, $event_id);
				$calendar->events()->attach(end($events));
			}
		}
		else { // event_id
			$events[] = $this->getEvent($request, $request->input('event_id'));
			$calendar->events()->attach($events[0]);
		}

		foreach ($events as $event)
			$this->hideEventData($request, $event);

		return response()->json($events, 201);
	}

	/**
	 * Show Calendar
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, int $calendar_id, int $id): JsonResponse {
		$calendar = $this->getCalendar($request, $calendar_id);
		$event = $this->getEventFromCalendar($request, $calendar, $id);

		return response()->json($this->hideEventData($request, $event), 200);
	}

	/**
	 * Update Calendar
	 *
	 * @param Request $request
	 * @param  int $id
	 */
	public function update(Request $request, int $calendar_id, int $id): JsonResponse {
		abort(405);
	}

	/**
	 * Delete Calendar
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, int $calendar_id, $id): JsonResponse {
		$calendar = $this->getCalendar($request, $id, true);
		$calendar->softDelete();

		abort(204);
	}
}
