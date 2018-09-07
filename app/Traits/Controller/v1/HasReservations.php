<?php

namespace App\Traits\Controller\v1;

use App\Traits\HasVisibility;
use App\Models\Reservation;
use App\Models\Model;
use Illuminate\Http\Request;

trait HasReservations
{
	use HasVisibility;

	public function isPrivate($user_id, $reservation = null) {
		if ($reservation === null)
			return false;

		return $reservation->owned_by->isReservationAccessibleBy($user_id);
  }

	protected function getReservation(Request $request, User $user, string $id, string $verb = 'get') {
		$reservation = Reservation::find($id);

		if ($reservation) {
			if (!$this->tokenCanSee($request, $reservation, $verb))
				abort(403, 'L\'application n\'a pas les droits sur cet réservation');

			if (!$this->isVisible($reservation, $user->id))
				abort(403, 'Vous n\'avez pas le droit de voir cette réservation');

			if ($verb !== 'get' && !$reservation->owned_by->isReservationManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droits suffisants');

			return $reservation;
		}

		abort(404, 'Impossible de trouver la réservation');
	}

	protected function tokenCanSee(Request $request, Reservation $reservation, string $verb) {
		$scopeHead = \Scopes::getTokenType($request);

		if (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-reservations-'.\ModelResolver::getName($reservation->owned_by_type).'s-owned'))
			return true;

		if (((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-reservations-'.\ModelResolver::getName($reservation->owned_by_type).'s-owned-asso'))
				&& $reservation->created_by_type === Asso::class
				&& $reservation->created_by_id === \Scopes::getClient($request)->asso->id)) {
			if (\Scopes::isUserToken($request)) {
				$functionToCall = 'isReservation'.($verb === 'get' ? 'Accessible' : 'Manageable').'By';

				if ($reservation->owned_by->$functionToCall(\Auth::id()))
					return true;
			}
			else
				return true;
		}

		return \Scopes::hasOne($request, $scopeHead.'-'.$verb.'-reservations-'.\ModelResolver::getName($reservation->owned_by_type).'s-created');
	}
}
