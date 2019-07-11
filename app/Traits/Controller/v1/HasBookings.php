<?php
/**
 * Adds the controller an access to Bookings.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Models\Booking;
use App\Models\Model;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Asso;
use App\Models\Room;
use Carbon\Carbon;

trait HasBookings
{
    use HasRooms {
        HasRooms::tokenCanSee as private tokenCanSeeRoom;
    }

    /**
     * Checks that the time period is clear.
     *
     * @param  string $room_id
     * @param  string $begin_at
     * @param  string $end_at
     * @return boolean
     */
    protected function checkBookingPeriod(string $room_id, string $begin_at, string $end_at)
    {
        $begin = Carbon::parse($begin_at);
        $end = Carbon::parse($end_at);

        if ($begin->lessThanOrEqualTo(Carbon::now())) {
            abort(400, 'La date de début d\'événement doit être postérieure à la date actuelle');
        }

        if ($begin->addMinutes(5)->greaterThan($end)) {
            abort(400, 'L\'événement doit avoir une durée d\'au moins 1 min');
        }

        $begin->subMinutes(5);

        $events = Room::find($room_id)->calendar->events()
            ->where('end_at', '>', $begin_at)
            ->where('begin_at', '<', $end_at)
            ->get();

        $query = Booking::where('room_id', $room_id)
            ->whereNotNull('validated_by_type')
            ->whereIn('event_id', $events->map(function ($event) {
                return $event->id;
            }));

        if ($query->exists()) {
            abort(409, 'Il existe une réservation qui se déroule pendant la même période');
        }

        // If we pass the maximun booking time, the booking must me validated. 
        return $end->diffInSeconds($begin) <= (config('portail.bookings.max_duration') * 60 * 60);
    }

    /**
     * Retrieves a booking from a room.
     *
     * @param  Request $request
     * @param  Room    $room
     * @param  User    $user
     * @param  string  $booking_id
     * @param  string  $verb
     * @return Booking|null
     */
    protected function getBookingFromRoom(Request $request, Room $room, User $user, string $booking_id,
        string $verb='get')
    {
        $booking = $room->bookings()->findSelection($booking_id);

        if ($booking) {
            if (!$this->tokenCanSee($request, $booking, $verb)) {
                abort(403, 'L\'application n\'a pas les droits sur cet réservation');
            }

            if ($verb !== 'get' && !$booking->owned_by->isBookingManageableBy(\Auth::id())) {
                abort(403, 'Vous n\'avez pas les droits suffisants');
            }

            return $booking;
        }

        abort(404, 'Impossible de trouver la réservation');
    }

    /**
     * Returns if the token can see or not.
     *
     * @param  Request $request
     * @param  Model   $model
     * @param  string  $verb
     * @return boolean
     */
    protected function tokenCanSee(Request $request, Model $model, string $verb='get')
    {
        if (!($model instanceof Booking)) {
            return $this->tokenCanSeeRoom($request, $model, $verb);
        }

        $scopeHead = \Scopes::getTokenType($request);
        $type = \ModelResolver::getName($model->owned_by_type);

        if (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-bookings-'.$type.'s-owned')) {
            return true;
        }

        if (((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-bookings-'.$type.'s-owned-asso'))
            && $model->created_by_type === Asso::class
            && $model->created_by_id === \Scopes::getClient($request)->asso->id)) {
            return true;
        }

        return \Scopes::hasOne($request, $scopeHead.'-'.$verb.'-bookings-'.$type.'s-created');
    }
}
