<?php

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasCalendars;
use App\Models\User;
use App\Models\Asso;
use App\Models\Calendar;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Visible\Visible;
use App\Interfaces\CanHaveCalendars;
use App\Traits\HasVisibility;

/**
 * @resource Calendar
 *
 * Gestion des calendriers
 */
class CalendarController extends Controller
{
	use HasCalendars;

	public function __construct() {
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren(['user-get-calendars-users-owned', 'user-get-calendars-users-followed'], ['client-get-calendars-users-owned', 'client-get-calendars-users-followed']),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-create-calendars-users-followed', 'client-create-calendars-users-followed'),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-edit-calendars-users-followed', 'client-edit-calendars-users-followed'),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-manage-calendars-users-followed', 'client-manage-calendars-users-followed'),
			['only' => ['destroy']]
		);
	}

	/**
	 * List Calendars
	 *
	 * @return JsonResponse
	 */
	public function index(Request $request, string $user_id = null): JsonResponse {
		$scopeHead = \Scopes::isUserToken($request) ? 'user' : 'client';
		$user = $this->getUser($request, $user_id);
		$calendars = collect();
		$choices = [];

		if (\Scopes::hasOne($request, array_keys(\Scopes::getRelatives($scopeHead.'-get-calendars-users-owned'))))
			$choices[] = 'owned';

		if (\Scopes::hasOne($request, array_keys(\Scopes::getRelatives($scopeHead.'-get-calendars-users-followed'))))
			$choices[] = 'followed';

		$choices = $this->getChoices($request, $choices);

		if (in_array('owned', $choices))
			$calendars = $user->calendars()->getSelection();

		if (in_array('followed', $choices))
			$calendars = $calendars->merge($user->followedCalendars()->getSelection());

		$calendars = $calendars->filter(function ($calendar) use ($request) {
			return ($this->tokenCanSee($request, $calendar, 'get') && (!\Auth::id() || $this->isVisible($calendar, \Auth::id()))) || $this->isCalendarFollowed($request, $calendar, \Auth::id());
		})->values()->map(function ($calendar) {
			return $calendar->hideData();
		});

		return response()->json($calendars, 200);
	}

	/**
	 * Create Calendar
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request, string $user_id = null): JsonResponse {
		$user = $this->getUser($request, $user_id);
		$calendars = [];
		$calendar_ids = $request->input('calendar_ids', [$request->input('calendar_id')]);

		foreach ($calendar_ids as $calendar_id) {
			$calendar = $this->getCalendar($request, $user, $calendar_id);

			$user->followedCalendars()->attach($calendar);
			$calendars[] = $calendar;
		}

		$calendars = $calendars->map(function ($calendar) {
			return $calendar->hideData();
		});

		return response()->json($calendars, 201);
	}

	/**
	 * Show Calendar
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, string $user_id, int $id = null): JsonResponse {
        if (is_null($id))
            list($user_id, $id) = [$id, $user_id];

		$user = $this->getUser($request, $user_id);
		$calendar = $this->getCalendar($request, $user, $id);
		$calendar_ids = $user->calendars()->pluck('calendars.id')->merge($user->followedCalendars()->pluck('calendars.id'));

		if (!$calendar_ids->contains($id))
			abort(404, 'Le calendrier n\'est pas suivi par la personne ou n\'existe pas');

		return response()->json($calendar->hideData(), 200);
	}

	/**
	 * Update Calendar
	 *
	 * @param Request $request
	 * @param  int $id
	 */
	public function update(Request $request, string $user_id, int $id = null): JsonResponse {
		abort(405);
	}

	/**
	 * Delete Calendar
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, string $user_id, int $id = null): JsonResponse {
		if (is_null($id))
			list($user_id, $id) = [$id, $user_id];

		$user = $this->getUser($request, $user_id);
		$calendar = $this->getCalendar($request, $user, $id);
		$calendar_ids = $user->followedCalendars()->pluck('calendars.id');

		if ($calendar_ids->contains($id)) {
			$user->followedCalendars()->detach($calendar);

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
