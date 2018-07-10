<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asso;
use App\Models\Calendar;
use App\Models\Client;
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
abstract class AbstractCalendarController extends Controller
{
	protected $types;

	public function populateScopes($begin, $end) {
		return array_map(function ($type) use ($begin, $end) {
			return $begin.'-'.$type.'s-'.$end;
		}, array_keys($this->types));
	}

	public function classToType($class) {
		return array_search($class, $this->types);
	}

	protected function hideCalendarData(Request $request, $calendar) {
		$calendar->created_by = $this->hideData($request, $calendar->created_by);
		$calendar->owned_by = $this->hideData($request, $calendar->owned_by);

		$calendar->makeHidden('visibility_id');

		return $calendar;
	}

	protected function getCalendar(Request $request, int $id, string $verb = 'get', bool $needRights = false) {
		$calendar = Calendar::with(['owned_by', 'created_by', 'visibility'])->find($id);

		if ($calendar) {
			if (!$this->tokenCanSeeCalendar($request, $calendar, $verb))
				abort(403, 'Vous n\'avez pas le droit de consulter ce calendrier');

			if ($needRights && \Scopes::isUserToken($request) && !$calendar->owned_by->isCalendarManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droits suffisants');

			return $calendar;
		}

		abort(404, 'Impossible de trouver le calendrier');
	}

	protected function tokenCanSeeCalendar(Request $request, Calendar $calendar, string $verb) {
		if (\Scopes::isClientToken($request)) {
			if (\Scopes::hasOne($request, 'client-'.$verb.'-calendars-'.$this->classToType($calendar->owned_by_type).'s-owned'))
				return true;

			if (\Scopes::hasOne($request, 'client-'.$verb.'-calendars-'.$this->classToType($calendar->owned_by_type).'s-owned-client') && $calendar->created_by_type === Client::class)
				return true;

			if (\Scopes::hasOne($request, 'client-'.$verb.'-calendars-'.$this->classToType($calendar->owned_by_type).'s-created'))
				return true;
		}
		else {
			// TODO MAnque els scopes
			return $this->isVisible($calendar);
		}

		return false;
	}

	public function isPrivate($user_id, $model = null) {
		if ($model === null)
			return false;

		// Si c'est privée, uniquement les followers et ceux qui possèdent le droit peuvent le voir
		if ($model->followers()->wherePivot('user_id', $user_id)->exists())
			return true;

		return $model->owned_by->isCalendarAccessibleBy($user_id);
    }
}
