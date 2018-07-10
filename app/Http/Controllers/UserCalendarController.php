<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asso;
use App\Models\Calendar;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractCalendarController;
use App\Services\Visible\Visible;
use App\Interfaces\CanHaveCalendars;
use App\Traits\HasVisibility;

/**
 * @resource Calendar
 *
 * Gestion des calendriers
 */
class UserCalendarController extends AbstractCalendarController
{
	// TODO getCalendar prend pas en compte les user customs

	public function __construct() {
		parent::__construct();

		$this->middleware(
			\Scopes::matchOne(array_merge(
				['user-get-calendars-users-owned-client'],
				$this->populateScopes('user-get-calendars-users-followed')
			), array_merge(
				['client-get-calendars-users-owned-client'],
				$this->populateScopes('client-get-calendars-users-followed')
			)),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne(
				$this->populateScopes('user-create-calendars-users-followed'),
				$this->populateScopes('client-create-calendars-users-followed')
			),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOne(
				$this->populateScopes('user-set-calendars-users-followed'),
				$this->populateScopes('client-set-calendars-users-followed')
			),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOne(
				$this->populateScopes('user-manage-calendars-users-followed'),
				$this->populateScopes('client-manage-calendars-users-followed')
			),
			['only' => ['destroy']]
		);
	}

	protected function tokenCanSeeCalendar(Request $request, Calendar $calendar, string $verb) {
		if (parent::tokenCanSeeCalendar($request, $calendar, $verb))
			return true;
		else
			return (\Scopes::hasOne($request, (\Scopes::isClientToken($request) ? 'client' : 'user').'-'.$verb.'-calendars-users-followed-'.$this->classToType($calendar->owned_by_type).'s'));
	}

	/**
	 * List Calendars
	 *
	 * @return JsonResponse
	 */
	public function index(Request $request, int $user_id = null): JsonResponse {
		$scopeHead = \Scopes::isUserToken($request) ? 'user' : 'client';
		$user = $this->getUser($request, $user_id);
		$calendars = collect();
		$choices = [];

		if (\Scopes::hasOne($request, $scopeHead.'-get-calendars-users-owned-client'))
			$choices[] = 'owned';

		foreach ($this->types as $type => $class) {
			if (\Scopes::hasOne($request, $scopeHead.'-get-calendars-users-followed-'.$type.'s'))
				$choices[] = 'followed-'.$type.'s';
		}

		$choices = $this->getChoices($request, $choices);

		if (in_array('owned', $choices)) {
			if (\Scopes::hasOne($request, $scopeHead.'-get-calendars-users-owned'))
				$calendars = $user->calendars()->with(['owned_by', 'created_by', 'visibility'])->get();
			else {
				$calendars = $user->calendars()->with(['owned_by', 'created_by', 'visibility'])->where('created_by_type', Client::class)->where('created_by_id', \Scopes::getClient($request)->id)->get();
			}
		}

		$followed = [];

		foreach ($this->types as $type => $class) {
			if (in_array('followed-'.$type.'s', $choices))
				$followed[] = $class;
		}

		if (count($followed) > 0) {
			$calendars = $calendars->merge(
				$user->followedCalendars()->with(['owned_by', 'created_by', 'visibility'])->whereIn('owned_by_type', $followed)->get()
			);
		}

		$calendars = $calendars->map(function ($calendar) use ($request) {
			return $this->hideCalendarData($request, $calendar);
		});

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
		$calendar_ids = $request->input('calendar_ids', [$request->input('calendar_id')]);

		foreach ($calendar_ids as $calendar_id) {
			$calendar = $this->getCalendar($request, $user, $calendar_id);

			$user->followedCalendars()->attach($calendar);
			$calendars[] = $calendar;
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
		$calendar = $this->getCalendar($request, $user, $id);
		$calendar_ids = $user->calendars()->pluck('calendars.id')->merge($user->followedCalendars()->pluck('calendars.id'));

		if (!$calendar_ids->contains($id))
			abort(404, 'Le calendrier n\'est pas suivi par la personne ou n\'existe pas');

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
