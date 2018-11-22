<?php
/**
 * Ajoute au controlleur un accès aux réservations.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Models\Reservation;
use App\Models\Model;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Room;
use Carbon\Carbon;

trait HasReservations
{
    use HasRooms {
        HasRooms::tokenCanSee as private tokenCanSeeRoom;
    }

    /**
     * Vérifie qu'il n'y a pas de réservation au même moment.
     *
     * @param  string $room_id
     * @param  string $begin_at
     * @param  string $end_at
     * @return boolean
     */
    protected function checkReservationPeriod(string $room_id, string $begin_at, string $end_at)
    {
        $eventsQuery = Room::find($room_id)->calendar->events()
            ->whereNotNull('validated_by_type')
            ->where('end_at', '>', $begin_at)
            ->where('begin_at', '<', $end_at);

        if ($eventsQuery->exists()) {
            abort(409, 'Il existe une réservation qui se déroule pendant la même période');
        }

        $begin = Carbon::parse($begin_at);
        $end = Carbon::parse($end_at);

        return $end->diffInSeconds($begin) <= (config('portail.reservations.max_duration') * 60 * 60);
    }

    /**
     * Récupère une réservation à partir d'une salle.
     *
     * @param  Request $request
     * @param  Room    $room
     * @param  User    $user
     * @param  string  $reservation_id
     * @param  string  $verb
     * @return Reservation|null
     */
    protected function getReservationFromRoom(Request $request, Room $room, User $user, string $reservation_id, string $verb='get')
    {
        $reservation = $room->reservations()->find($reservation_id);

        if ($reservation) {
            if (!$this->tokenCanSee($request, $reservation, $verb)) {
                abort(403, 'L\'application n\'a pas les droits sur cet réservation');
            }

            if (!$this->isVisible($reservation, $user->id)) {
                abort(403, 'Vous n\'avez pas le droit de voir cette réservation');
            }

            if ($verb !== 'get' && !$reservation->owned_by->isReservationManageableBy(\Auth::id())) {
                abort(403, 'Vous n\'avez pas les droits suffisants');
            }

            return $reservation;
        }

        abort(404, 'Impossible de trouver la réservation');
    }

    /**
     * Indique si le token peut voir ou non.
     *
     * @param  Request $request
     * @param  Model   $model
     * @param  string  $verb
     * @return boolean
     */
    protected function tokenCanSee(Request $request, Model $model, string $verb)
    {
        if ($model instanceof Room) {
            return $this->tokenCanSeeRoom($request, $model, $verb);
        }

        $scopeHead = \Scopes::getTokenType($request);
        $type = \ModelResolver::getName($model->owned_by_type);

        if (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-reservations-'.$type.'s-owned')) {
            return true;
        }

        if (((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-reservations-'.$type.'s-owned-asso'))
            && $model->created_by_type === Asso::class
            && $model->created_by_id === \Scopes::getClient($request)->asso->id)) {
            if (\Scopes::isUserToken($request)) {
                $functionToCall = 'isReservation'.($verb === 'get' ? 'Accessible' : 'Manageable').'By';

                if ($model->owned_by->$functionToCall(\Auth::id())) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return \Scopes::hasOne($request, $scopeHead.'-'.$verb.'-reservations-'.$type.'s-created');
    }
}
