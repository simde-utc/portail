<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asso;
use App\Models\Calendar;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AbstractCalendarController;
use App\Services\Visible\Visible;
use App\Interfaces\CanHaveCalendars;
use App\Traits\HasVisibility;

/**
 * @resource Calendar
 *
 * Gestion des calendriers
 */
class CalendarController extends AbstractCalendarController
{
	public function __construct() {
		parent::__construct();
		
		$this->middleware(
			\Scopes::matchOne(array_merge(
				$this->populateScopes('client-get-calendars', 'created'),
				$this->populateScopes('client-get-calendars', 'owned-client')
			)),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne(array_merge(
				$this->populateScopes('client-create-calendars', 'created')
			)),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOne(array_merge(
				$this->populateScopes('client-set-calendars', 'created'),
				$this->populateScopes('client-set-calendars', 'owned-client')
			)),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOne(array_merge(
				$this->populateScopes('client-manage-calendars', 'created'),
				$this->populateScopes('client-manage-calendars', 'owned-client')
			)),
			['only' => ['delete']]
		);
	}

	/**
	 * List Calendars
	 *
	 * @return JsonResponse
	 */
	public function index(Request $request): JsonResponse {
		$calendars = Calendar::with(['owned_by', 'created_by', 'visibility'])->get()->filter(function ($calendar) use ($request) {
			return $this->tokenCanSeeCalendar($request, $calendar, 'get');
		})->values()->map(function ($calendar) use ($request) {
			return $this->hideCalendarData($request, $calendar);
		});

		return response()->json($calendar, 200);
	}

	/**
	 * Create Calendar
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request): JsonResponse {
		$inputs = $request->all();

		if (\Scopes::isUserToken($request)) {
			$creater = \Auth::user();
			$owner = \Auth::user();
		}
		else {
			if ($request->filled('created_by_id')) {
				if (!\Scopes::hasOne($request, 'client-create-calendars-'.$request->input('created_by_type').'s-created'))
					abort(403);

				$creater = resolve($this->types[$request->input('created_by_type')])->find($request->input('created_by_id'));
			}
			else if ($request->input('created_by_type', 'client') === 'client')
				$creater = \Scopes::getToken()->client();
			else if ($request->input('created_by_type', 'client') === 'asso')
				$creater = \Scopes::getToken()->client()->asso;
		}

		if (!($owner instanceof CanHaveCalendars))
			abort(400, 'Le owner doit au moins pouvoir avoir un calendrier');
		if (!($owner instanceof CanHaveCalendars))
			abort(400, 'Le owner doit au moins pouvoir avoir un calendrier');

		$inputs['created_by_id'] = $creater->id;
		$inputs['created_by_type'] = get_class($creater);
		$inputs['owned_by_id'] = $owner->id;
		$inputs['owned_by_type'] = get_class($owner);

		$calendar = Calendar::create($inputs);

		if ($calendar)
			return response()->json($calendar, 201);
		else
			return response()->json(['message' => 'Impossible de créer le calendrier'], 500);

	}

	/**
	 * Show Calendar
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, int $id): JsonResponse {
		$calendar = $this->getCalendar($request, $id);
		$calendar = $this->hideCalendarData($request, $calendar);

		return response()->json($calendar, 200);
	}

	/**
	 * Update Calendar
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, $id): JsonResponse {
		$calendar = $this->getCalendar($request, $id, 'set', true);

		if ($calendar->update($request->input()))
			return response()->json($this->hideCalendarData($calendar), 200);
		else
			abort(500, 'Impossible de modifier le calendrier');
	}

	/**
	 * Delete Calendar
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, int $id): JsonResponse {
		$calendar = $this->getCalendar($request, $id, 'manage', true);
		$calendar->softDelete();

		abort(204);
	}
}
