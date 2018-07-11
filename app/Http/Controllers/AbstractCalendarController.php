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
	use HasVisibility;

	protected $types;

	public function __construct() {
		$this->types = Calendar::getTypes();
	}

	public function populateScopes($begin, $end = null) {
		return array_map(function ($type) use ($begin, $end) {
			return $begin.'-'.$type.($end ? 's-'.$end : 's');
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

	protected function hideEventData(Request $request, $event) {
		$event->created_by = $this->hideData($request, $event->created_by);
		$event->owned_by = $this->hideData($request, $event->owned_by);

		$event->makeHidden(['location_id', 'visibility_id']);

		return $event;
	}

	protected function getCalendar(Request $request, User $user = null, int $id, string $verb = 'get', bool $needRights = false) {
		$calendar = Calendar::with(['owned_by', 'created_by', 'visibility'])->find($id);

		if ($calendar) {
			if (!$this->tokenCanSee($request, $calendar, $verb) || ($user && !$this->isVisible($calendar, $user->id)))
				abort(403, 'Vous n\'avez pas le droit de consulter ce calendrier');

			if ($needRights && \Scopes::isUserToken($request) && !$calendar->owned_by->isCalendarManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droits suffisants');

			return $calendar;
		}

		abort(404, 'Impossible de trouver le calendrier');
	}

	protected function getEvent(Request $request, User $user = null, int $id, bool $needRights = false) {
		$event = Event::with(['owned_by', 'created_by', 'visibility', 'details', 'location'])->find($id);

		if ($event) {
			if (!$this->tokenCanSee($request, $event, $verb, 'events') || ($user && !$this->isVisible($event, $user->id)))
				abort(403, 'Vous n\'avez pas le droit de consulter ce évènenement');

			if ($needRights && !$event->owned_by->isEventManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droit suffisant');

			$event->participants = $event->participants->map(function ($user) use ($request) {
				return $this->hideUserData($request, $user);
			});

			return $event;
		}

		abort(404, 'Impossible de trouver le évènenement');
	}

	protected function tokenCanSee(Request $request, $model, string $verb, string $type = 'calendars') {
		$scopeHead = \Scopes::isUserToken($request) ? 'user' : 'client';

		return (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$this->classToType($model->owned_by_type).'s-owned'))
			|| ((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$this->classToType($model->owned_by_type).'s-owned-client'))
			 	&& $model->created_by_type === Client::class
				&& $model->created_by_id === \Scopes::getClient($request)->id)
			|| ((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$this->classToType($model->owned_by_type).'s-owned-asso'))
			 	&& $model->created_by_type === Asso::class
				&& $model->created_by_id === \Scopes::getClient($request)->asso->id)
			|| (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$this->classToType($model->owned_by_type).'s-created'));
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
