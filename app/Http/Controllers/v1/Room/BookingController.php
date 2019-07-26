<?php
/**
 * Manage bookings.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Room;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasBookings;
use App\Traits\Controller\v1\HasCreatorsAndOwnersAndValidators;
use App\Traits\Controller\v1\HasValidators;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\BookingRequest;
use App\Models\Room;
use App\Models\Event;
use App\Models\Booking;
use App\Models\BookingType;
use Carbon\Carbon;

class BookingController extends Controller
{
    use HasBookings, HasCreatorsAndOwnersAndValidators;

    /**
     * Must be able to see rooms and manage bookings.
     */
    public function __construct()
    {
        $this->middleware(
	        array_merge(
		        \Scopes::matchOneOfDeepestChildren('user-get-rooms', 'client-get-rooms'),
		        \Scopes::matchOne('user-get-bookings', 'client-get-bookings')
	        ),
	        ['only' => ['all', 'get']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOneOfDeepestChildren('user-get-rooms', 'client-get-rooms'),
		        \Scopes::matchOne('user-create-bookings', 'client-create-bookings')
	        ),
	        ['only' => ['create']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOneOfDeepestChildren('user-get-rooms', 'client-get-rooms'),
		        \Scopes::matchOne('user-edit-bookings', 'client-edit-bookings')
	        ),
	        ['only' => ['edit']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOneOfDeepestChildren('user-get-rooms', 'client-get-rooms'),
		        \Scopes::matchOne('user-remove-bookings', 'client-remove-bookings')
	        ),
	        ['only' => ['remove']]
        );
    }

    /**
     * Check that the booking can done.
     *
     * @param  Request $request
     * @param  string  $room_id
     * @param  Model   $owner
     * @param  array  	$inputs
     * @return array
     */
    protected function checkValidations(Request $request, string $room_id, Model $owner, array $inputs)
    {
        if (($inputs['full_day'] ?? false)) {
            $inputs['begin_at'] = Carbon::parse($inputs['begin_at'])->toDateString();
            $inputs['end_at'] = Carbon::parse($inputs['end_at'])->toDateString();
        }

        // Check if the booking type auto-validated and that the duration is not too long.
        if (!$this->checkBookingPeriod($room_id, $inputs['begin_at'], $inputs['end_at'])) {
            $inputs['validated_by_id'] = null;
            $inputs['validated_by_type'] = null;
        } else if (isset($inputs['validated_by_type'])) {
            // Here we check if the validating person can validate the booking ask.
            $validator = $this->getValidatorFromOwner($request, $owner, 'booking', 'réservation', 'create');

            // Check if we can validate an booking in a room owned by someone.
            if (!Room::find($room_id)->owned_by->isBookingValidableBy($validator)) {
                abort(403, 'Vous n\'avez pas le droit de valider cette réservation');
            }

            $inputs['validated_by_id'] = $validator->id;
            $inputs['validated_by_type'] = get_class($validator);
        } else if (!BookingType::find($inputs['type_id'])->need_validation) {
            $inputs['validated_by_id'] = $inputs['owned_by_id'];
            $inputs['validated_by_type'] = $inputs['owned_by_type'];
        }

        return $inputs;
    }

    /**
     * List bookings of the room.
     *
     * @param  Request $request
     * @param  string  $room_id
     * @return JsonResponse
     */
    public function index(Request $request, string $room_id): JsonResponse
    {
        $room = $this->getRoom($request, \Auth::user(), $room_id);

        $bookings = $room->bookings()->getSelection();

        return response()->json($bookings->map(function ($booking) {
            return $booking->hideData();
        }), 200);
    }

    /**
     * Create a booking for the room.
     *
     * @param  BookingRequest $request
     * @param  string         $room_id
     * @return JsonResponse
     */
    public function store(BookingRequest $request, string $room_id): JsonResponse
    {
        $room = $this->getRoom($request, \Auth::user(), $room_id);
        $inputs = $request->all();

        $owner = $this->getOwner($request, 'booking', 'réservation', 'create');
        $creator = $this->getCreatorFromOwner($request, $owner, 'booking', 'réservation', 'create');
        // Check if the booker has the rights in this room.
        if (!$room->owned_by->isRoomReservableBy($owner)) {
            abort(403, 'Vous n\'être pas autorisé à réserver cette salle');
        }

        $inputs['created_by_id'] = $creator->id;
        $inputs['created_by_type'] = get_class($creator);
        $inputs['owned_by_id'] = $owner->id;
        $inputs['owned_by_type'] = get_class($owner);

        $inputs = $this->checkValidations($request, $room_id, $owner, $inputs);

        $calendar = $room->calendar;

        // WARNING : the event belongs to the calendar owner (to prevent people from mofifying by themself the event).
        $event = Event::create([
            'name' => ($inputs['name'] ?? BookingType::find($inputs['type_id'])->name),
            'begin_at' => $inputs['begin_at'],
            'end_at' => $inputs['end_at'],
            'full_day' => ($inputs['full_day'] ?? false),
            'location_id' => $room->location->id,
            'created_by_id' => $inputs['created_by_id'],
            'created_by_type' => $inputs['created_by_type'],
            'owned_by_id' => $calendar->owned_by_id,
            'owned_by_type' => $calendar->owned_by_type,
            'visibility_id' => $calendar->visibility_id,
        ]);

        $calendar->events()->attach($event);
        $inputs['event_id'] = $event->id;

        $booking = $room->bookings()->create($inputs);

        if ($booking) {
            $booking = $this->getBookingFromRoom($request, $room, \Auth::user(), $booking->id);

            return response()->json($booking->hideSubData(), 201);
        } else {
            abort(500, 'Impossible de créer la réservation');
        }
    }

    /**
     * Show a booking of the room.
     *
     * @param  Request $request
     * @param  string  $room_id
     * @param  string  $booking_id
     * @return JsonResponse
     */
    public function show(Request $request, string $room_id, string $booking_id): JsonResponse
    {
        $room = $this->getRoom($request, \Auth::user(), $room_id);
        $booking = $this->getBookingFromRoom($request, $room, \Auth::user(), $booking_id);

        return response()->json($booking->hideSubData(), 200);
    }

    /**
     * Update a booking of the room.
     *
     * @param  BookingRequest $request
     * @param  string         $room_id
     * @param  string         $booking_id
     * @return JsonResponse
     */
    public function update(BookingRequest $request, string $room_id, string $booking_id): JsonResponse
    {
        $room = $this->getRoom($request, \Auth::user(), $room_id);
        $booking = $this->getBookingFromRoom($request, $room, \Auth::user(), $booking_id, 'edit');
        $inputs = $request->all();

        $inputs['begin_at'] = ($inputs['begin_at'] ?? $booking->begin_at);
        $inputs['end_at'] = ($inputs['end_at'] ?? $booking->end_at);

        $inputs = $this->checkValidations($request, $room_id, $booking->owned_by, $inputs);

        if ($booking->event->update($inputs) && $booking->update($inputs)) {
            return response()->json($booking->hideSubData(), 201);
        } else {
            abort(500, 'Impossible de modifier la réservation');
        }
    }

    /**
     * Delete a booking of the room.
     *
     * @param  Request $request
     * @param  string  $room_id
     * @param  string  $booking_id
     * @return void
     */
    public function destroy(Request $request, string $room_id, string $booking_id): void
    {
        $room = $this->getRoom($request, \Auth::user(), $room_id);
        $booking = $this->getBookingFromRoom($request, $room, \Auth::user(), $booking_id, 'manage');

        if ($booking->delete()) {
            abort(204);
        } else {
            abort(500, 'Impossible de suprimer la réservation');
        }
    }
}
