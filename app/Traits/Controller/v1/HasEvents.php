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

		return $model->owned_by->isEventAccessibleBy($user_id);
    }

	// Uniquement les followers et ceux qui possèdent le droit peuvent le voir
	protected function isEventFollowed(Request $request, Event $event, int $user_id) {
		$user = User::find($user_id);
		$calendar_ids = $user->calendars()->get(['calendars.id'])->pluck('id')->merge($user->followedCalendars()->get(['calendars.id'])->pluck('id'));
		$event_calendar_ids = $event->calendars()->get(['calendars.id'])->pluck('id');

		return (
			count($calendar_ids->intersect($event_calendar_ids)) !== 0
			&& \Scopes::hasOne($request, \Scopes::getTokenType($request).'-get-events-users-followed-'.\ModelResolver::getName($event->owned_by_type))
		);
	}

	protected function getEvent(Request $request, User $user = null, int $id, string $verb = 'get') {
		$event = Event::find($id);

		if ($event) {
			if (!$this->tokenCanSee($request, $event, $verb, 'events'))
				abort(403, 'L\'application n\'a pas les droits sur cet évènenement');

			if ($user && !$this->isVisible($event, $user->id) && !$this->isEventFollowed($request, $event, $user->id))
				abort(403, 'Vous n\'avez pas les droits sur cet évènenement');

			if ($verb !== 'get' && !$event->owned_by->isEventManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droits suffisants');

			return $event;
		}

		abort(404, 'Impossible de trouver le évènenement');
	}

	protected function tokenCanSee(Request $request, Model $model, string $verb, string $type = 'events') {
		$scopeHead = \Scopes::getTokenType($request);

		if (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.\ModelResolver::getName($model->owned_by_type).'s-owned'))
			return true;

		if (((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.\ModelResolver::getName($model->owned_by_type).'s-owned-user'))
				&& \Auth::id()
				&& $model->created_by_type === User::class
				&& $model->created_by_id === \Auth::id())
			|| ((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.\ModelResolver::getName($model->owned_by_type).'s-owned-client'))
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
