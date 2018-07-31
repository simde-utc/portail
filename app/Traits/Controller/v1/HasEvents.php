<?php

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\Event;
use App\Models\User;
use App\Models\Client;
use App\Traits\HasVisibility;
use Illuminate\Http\Request;
use App\Models\Model;

trait HasEvents
{
	use HasVisibility, HasUsers;

	public function isPrivate($user_id, $model = null) {
		if ($model === null)
			return false;

		// Si c'est privée, uniquement les personnes ayant un calendrier contenant cet event peuvent le voir
		$user = User::find($user_id);
		$calendar_ids = $user->calendars()->get(['id'])->pluck('id')->merge($user->followedCalendars()->get(['id'])->pluck('id'));
		$event_calendar_ids = $model->calendars()->get(['id'])->pluck('id');

		$model->makeHidden('calendars');

		if (count($calendar_ids->intersect($event_calendar_ids)) !== 0)
			return true;

		return $model->owned_by->isEventAccessibleBy($user_id);
    }

	protected function getEvent(Request $request, User $user = null, int $id, bool $needRights = false) {
		$event = Event::with(['owned_by', 'created_by', 'visibility', 'details', 'location'])->find($id);

		if ($event) {
			if (!$this->tokenCanSee($request, $event, $verb, 'events'))
				abort(403, 'L\'application n\'a pas les droits sur cet évènenement');

			if ($user && !$this->isVisible($event, $user->id))
				abort(403, 'Vous n\'avez pas les droits sur cet évènenement');

			if ($needRights && !$event->owned_by->isEventManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droits suffisants');

			$event->participants = $event->participants->map(function ($user) use ($request) {
				return $this->hideUserData($request, $user);
			});

			return $event;
		}

		abort(404, 'Impossible de trouver le évènenement');
	}

	protected function tokenCanSee(Request $request, Model $model, string $verb, string $type = 'events') {
		$scopeHead = \Scopes::getTokenType($request);

		if (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.\ModelResolver::getName($model->owned_by_type).'s-owned'))
			return true;

		if (((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.\ModelResolver::getName($model->owned_by_type).'s-owned-client'))
				&& $model->created_by_type === Client::class
				&& $model->created_by_id === \Scopes::getClient($request)->id)
			|| ((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.\ModelResolver::getName($model->owned_by_type).'s-owned-asso'))
				&& $model->created_by_type === Asso::class
				&& $model->created_by_id === \Scopes::getClient($request)->asso->id)) {
			if (\Scopes::isUserToken($request)) {
				$functionToCall = 'is'.($type === 'calendars' ? 'Calendar' : 'Event').($verb === 'get' ? 'Accessible' : 'Manageable').'By';

				if ($model->owned_by->$functionToCall(\Auth::id()))
					return true;
			}
			else
				return true;
		}

		return \Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.\ModelResolver::getName($model->owned_by_type).'s-created');
	}
}
