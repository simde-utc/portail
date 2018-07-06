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
class CalendarController extends Controller
{
	use HasVisibility;

	public function __construct() {
		$this->middleware(
			\Scopes::matchAnyUser()
		);
	}

	protected function hideCalendarData(Request $request, $calendar) {
		$calendar->created_by = $this->hideData($request, $calendar->created_by);
		$calendar->owned_by = $this->hideData($request, $calendar->owned_by);

		$calendar->makeHidden('visibility_id');

		return $calendar;
	}

	protected function getCalendar(Request $request, int $id, bool $needRights = false) {
		$calendar = Calendar::with(['owned_by', 'created_by', 'visibility'])->find($id);

		if ($calendar) {
			if (!$this->isVisible($calendar))
				abort(403, 'Vous n\'avez pas le droit de consulter ce calendrier');

			if ($needRights && !$calendar->owned_by->isCalendarManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droits suffisants');

			return $calendar;
		}

		abort(404, 'Impossible de trouver le calendrier');
	}

	public function isPrivate($user_id, $model = null) {
		if ($model === null)
			return false;

		// Si c'est privée, uniquement les followers et ceux qui possèdent le droit peuvent le voir
		if ($model->followers()->wherePivot('user_id', $user_id)->exists())
			return true;

		return $model->owned_by->isCalendarAccessibleBy($user_id);
    }

	/**
	 * List Calendars
	 *
	 * @return JsonResponse
	 */
	public function index(Request $request): JsonResponse {
		$calendars = Calendar::with(['owned_by', 'created_by', 'visibility'])->get()->map(function ($calendar) use ($request) {
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
	public function store(Request $request): JsonResponse {
		$inputs = $request->all();

		if (\Scopes::isUserToken($request))
			$creater = \Auth::user();
		else {
			// A faire au choix
		}

		$owner = \Auth::id();

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
		$calendar = $this->getCalendar($request, $id, true);

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
	public function destroy($id): JsonResponse {
		$calendar = $this->getCalendar($request, $id, true);
		$calendar->softDelete();

		abort(204);
	}
}
