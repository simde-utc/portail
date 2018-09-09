<?php

namespace App\Traits\Controller\v1;

use App\Traits\HasVisibility;
use App\Models\Room;
use App\Models\Model;
use App\Models\User;
use Illuminate\Http\Request;

trait HasRooms
{
	use HasVisibility;

	public function isPrivate($user_id, $room = null) {
		if ($room === null)
			return false;

		return $room->owned_by->isRoomAccessibleBy($user_id);
  }

	protected function getRoom(Request $request, User $user, string $id, string $verb = 'get') {
		$room = Room::find($id);

		if ($room) {
			if (!$this->tokenCanSee($request, $room, $verb))
				abort(403, 'L\'application n\'a pas les droits sur cet salle');

			if (!$this->isVisible($room, $user->id))
				abort(403, 'Vous n\'avez pas le droit de voir cette salle');

			if ($verb !== 'get' && !$room->owned_by->isRoomManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droits suffisants');

			return $room;
		}

		abort(404, 'Impossible de trouver la salle');
	}

	protected function tokenCanSee(Request $request, Room $room, string $verb) {
		$scopeHead = \Scopes::getTokenType($request);

		if (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-rooms-'.\ModelResolver::getName($room->owned_by_type).'s-owned'))
			return true;

		if (((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-rooms-'.\ModelResolver::getName($room->owned_by_type).'s-owned-asso'))
				&& $room->created_by_type === Asso::class
				&& $room->created_by_id === \Scopes::getClient($request)->asso->id)) {
			if (\Scopes::isUserToken($request)) {
				$functionToCall = 'isRoom'.($verb === 'get' ? 'Accessible' : 'Manageable').'By';

				if ($room->owned_by->$functionToCall(\Auth::id()))
					return true;
			}
			else
				return true;
		}

		return \Scopes::hasOne($request, $scopeHead.'-'.$verb.'-rooms-'.\ModelResolver::getName($room->owned_by_type).'s-created');
	}
}
