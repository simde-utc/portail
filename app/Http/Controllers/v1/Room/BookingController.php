<?php
/**
 * Gère les réservations.
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
     * Nécessité de pouvoir voir les salles et de gérer les réservations.
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
     * Vérifie que la réservation peut être réalisée.
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

        // On va maintenant voir si le type de réservation est auto-validée et que la durée n'est pas trop longue.
        if (!$this->checkBookingPeriod($room_id, $inputs['begin_at'], $inputs['end_at'])) {
            $inputs['validated_by_id'] = null;
            $inputs['validated_by_type'] = null;
        } else if (isset($inputs['validated_by_type'])) {
            // Ici on vérifie si la valideur peu valider la demande de résa du demandeur.
            $validator = $this->getValidatorFromOwner($request, $owner, 'booking', 'réservation', 'create');

            // Maintenant on vérifie qu'on a le droit de valider pour une résa dans une salle appartenant à qq'un.
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
     * Liste les réservations de la salle.
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
     * Crée une réservation réalisée pour la salle.
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
        // On vérifie que celui qui veut faire la réservation à le droit dans cette salle.
        if (!$room->owned_by->isRoomReservableBy($owner)) {
            abort(403, 'Vous n\'être pas autorisé à réserver cette salle');
        }

        $inputs['created_by_id'] = $creator->id;
        $inputs['created_by_type'] = get_class($creator);
        $inputs['owned_by_id'] = $owner->id;
        $inputs['owned_by_type'] = get_class($owner);

        $inputs = $this->checkValidations($request, $room_id, $owner, $inputs);

        $calendar = $room->calendar;

        // WARNING: L'événement appartient bien sûr à celui qui possède le calendrier (pour éviter en fait que les gens modifient eux-même l'event).
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
     * Montre une réservation de la salle.
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
     * Met à jour une réservation de la salle.
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
     * Supprime une réservation de la salle.
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
            abort(500, 'Impossible de supprimer la réservation');
        }
    }
}
