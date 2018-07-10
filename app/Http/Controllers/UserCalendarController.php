<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asso;
use App\Models\Calendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Visible\Visible;
use App\Interfaces\CanHaveCalendars;
use App\Traits\HasVisibility;

/**
 * @resource Calendar
 *
 * Gestion des calendriers
 */
class UserCalendarController extends Controller
{
	// TODO getCalendar prend pas en compte les user customs

	public function __construct() {
		$this->types = Calendar::getTypes();

		$this->middleware(
			\Scopes::matchOne(array_merge(
				$this->populateScopes('user-get-calendars', 'followed'),
				$this->populateScopes('client-get-calendars', 'followed')
			)),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne(array_merge(
				$this->populateScopes('user-create-calendars', 'followed'),
				$this->populateScopes('client-create-calendars', 'followed')
			)),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOne(array_merge(
				$this->populateScopes('user-set-calendars', 'followed'),
				$this->populateScopes('client-set-calendars', 'followed')
			)),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOne(array_merge(
				$this->populateScopes('user-manage-calendars', 'followed'),
				$this->populateScopes('client-manage-calendars', 'followed')
			)),
			['only' => ['delete']]
		);
	}
	
	/**
	 * List Calendars
	 *
	 * @return JsonResponse
	 */
	public function index(Request $request, int $user_id = null): JsonResponse {
		$user = $this->getUser($request, $user_id);
		$choices = $this->getChoices($request, ['owned', 'followed']);
		$calendars = collect();

		if (in_array('owned', $choices))
			$calendars = $user->calendars()->with(['owned_by', 'created_by', 'visibility'])->get()->map(function ($calendar) use ($request) {
				return $this->hideCalendarData($request, $calendar);
			});

		if (in_array('followed', $choices))
			$calendars = $calendars->merge($user->followedCalendars()->with(['owned_by', 'created_by', 'visibility'])->get()->map(function ($calendar) use ($request) {
				return $this->hideCalendarData($request, $calendar);
			}));

		return response()->json($calendars, 200);
	}

	/**
	 * Create Calendar
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request, int $user_id = null): JsonResponse {
		$user = $this->getUser($request, $user_id);
		$calendars = [];

		if ($request->filled('calendar_ids')) {
			foreach ($request->input('calendar_ids') as $calendar_id) {
				$calendars[] = $this->getCalendar($request, $calendar_id);
				$user->followedCalendars()->attach(end($events));
			}
		}
		else { // calendar_id
			$calendars[] = $this->getCalendar($request, $request->input('calendar_id'));
			$user->followedCalendars()->attach($calendars[0]);
		}

		foreach ($calendars as $calendar)
			$this->hideCalendarData($request, $calendar);

		return response()->json($calendars, 201);
	}

	/**
	 * Show Calendar
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, int $user_id, int $id = null): JsonResponse {
        if (is_null($id))
            list($user_id, $id) = [$id, $user_id];

		$user = $this->getUser($request, $user_id);
		$calendar_ids = $user->calendars()->pluck('calendars.id')->merge($user->followedCalendars()->pluck('calendars.id'));

		if (!$calendar_ids->contains($id))
			abort(404, 'Le calendrier n\'est pas suivi par la personne ou n\'existe pas');

		$calendar = $this->getCalendar($request, $id);
		$calendar = $this->hideCalendarData($request, $calendar);

		return response()->json($calendar, 200);
	}

	/**
	 * Update Calendar
	 *
	 * @param Request $request
	 * @param  int $id
	 */
	public function update(Request $request, int $user_id, int $id = null): JsonResponse {
		abort(405);
	}

	/**
	 * Delete Calendar
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, int $user_id, int $id = null): JsonResponse {
		if (is_null($id))
			list($user_id, $id) = [$id, $user_id];

		$user = $this->getUser($request, $user_id);
		$calendar_ids = $user->followedCalendars()->pluck('calendars.id');

		if ($calendar_ids->contains($id)) {
			$user->followedCalendars()->detach(Calendar::find($id));

			abort(204);
		}
		else {
			$calendar_ids = $user->calendars()->pluck('calendars.id');

			if ($calendar_ids->contains($id))
				abort(403, 'Il n\'est pas possible de ne plus suivre un calendrier appartenu par la personne');
			else
				abort(404, 'Le calendrier n\'est pas suivi par la personne ou n\'existe pas');
		}
	}
}
