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
use App\Traits\Controller\v1\HasReservations;
use App\Traits\Controller\v1\HasCreatorsAndOwnersAndValidators;
use App\Traits\Controller\v1\HasValidators;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ReservationRequest;
use App\Models\Room;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\ReservationType;
use Carbon\Carbon;

class ReservationController extends Controller
{
    use HasReservations, HasCreatorsAndOwnersAndValidators;

    /**
     * Nécessité de pouvoir voir les salles et de gérer les réservations.
     */
    public function __construct()
    {
        $this->middleware(
	        array_merge(
		        \Scopes::matchOneOfDeepestChildren('user-get-rooms', 'client-get-rooms'),
		        \Scopes::matchOne('user-get-reservations', 'client-get-reservations')
	        ),
	        ['only' => ['index', 'show']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOneOfDeepestChildren('user-get-rooms', 'client-get-rooms'),
		        \Scopes::matchOne('user-create-reservations', 'client-create-reservations')
	        ),
	        ['only' => ['store']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOneOfDeepestChildren('user-get-rooms', 'client-get-rooms'),
		        \Scopes::matchOne('user-edit-reservations', 'client-edit-reservations')
	        ),
	        ['only' => ['update']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOneOfDeepestChildren('user-get-rooms', 'client-get-rooms'),
		        \Scopes::matchOne('user-remove-reservations', 'client-remove-reservations')
	        ),
	        ['only' => ['destroy']]
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
        if (!$this->checkReservationPeriod($room_id, $inputs['begin_at'], $inputs['end_at'])) {
            $inputs['validated_by_id'] = null;
            $inputs['validated_by_type'] = null;
        } else if (isset($inputs['validated_by_type'])) {
            // Ici on vérifie si la valideur peu valider la demande de résa du demandeur.
            $validator = $this->getValidatorFromOwner($request, $owner, 'reservation', 'réservation', 'create');

            // Maintenant on vérifie qu'on a le droit de valider pour une résa dans une salle appartenant à qq'un.
            if (!Room::find($room_id)->owned_by->isReservationValidableBy($validator)) {
                abort(403, 'Vous n\'avez pas le droit de valider cette réservation');
            }

            $inputs['validated_by_id'] = $validator->id;
            $inputs['validated_by_type'] = get_class($validator);
        } else if (!ReservationType::find($inputs['type_id'])->need_validation) {
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

        $reservations = $room->reservations()->getSelection()->filter(function ($reservation) {
            return !\Auth::id() || $this->isVisible($reservation, \Auth::id());
        })->values()->map(function ($reservation) {
            return $reservation->hideData();
        });

        return response()->json($reservations, 200);
    }

    /**
     * Crée une réservation réalisée pour la salle.
     *
     * @param  ReservationRequest $request
     * @param  string             $room_id
     * @return JsonResponse
     */
    public function store(ReservationRequest $request, string $room_id): JsonResponse
    {
        $room = $this->getRoom($request, \Auth::user(), $room_id);
        $inputs = $request->all();

        $owner = $this->getOwner($request, 'reservation', 'réservation', 'create');
        $creator = $this->getCreatorFromOwner($request, $owner, 'reservation', 'réservation', 'create');
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
            'name' => ($inputs['name'] ?? ReservationType::find($inputs['type_id'])),
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

        $reservation = $room->reservations()->create($inputs);

        if ($reservation) {
            $reservation = $this->getReservationFromRoom($request, $room, \Auth::user(), $reservation->id);

            return response()->json($reservation->hideSubData(), 201);
        } else {
            abort(500, 'Impossible de créer la réservation');
        }
    }

    /**
     * Montre une réservation de la salle.
     *
     * @param  Request $request
     * @param  string  $room_id
     * @param  string  $reservation_id
     * @return JsonResponse
     */
    public function show(Request $request, string $room_id, string $reservation_id): JsonResponse
    {
        $room = $this->getRoom($request, \Auth::user(), $room_id);
        $reservation = $this->getReservationFromRoom($request, $room, \Auth::user(), $reservation_id);

        return response()->json($reservation->hideSubData(), 200);
    }

    /**
     * Met à jour une réservation de la salle.
     *
     * @param  ReservationRequest $request
     * @param  string             $room_id
     * @param  string             $reservation_id
     * @return JsonResponse
     */
    public function update(ReservationRequest $request, string $room_id, string $reservation_id): JsonResponse
    {
        $room = $this->getRoom($request, \Auth::user(), $room_id);
        $reservation = $this->getReservationFromRoom($request, $room, \Auth::user(), $reservation_id, 'edit');
        $inputs = $request->all();

        $inputs['begin_at'] = ($inputs['begin_at'] ?? $reservation->begin_at);
        $inputs['end_at'] = ($inputs['end_at'] ?? $reservation->end_at);

        $inputs = $this->checkValidations($request, $room_id, $reservation->owned_by, $inputs);

        if ($reservation->event->update($inputs) && $reservation->update($inputs)) {
            return response()->json($reservation->hideSubData(), 201);
        } else {
            abort(500, 'Impossible de modifier la réservation');
        }
    }

    /**
     * Supprime une réservation de la salle.
     *
     * @param  Request $request
     * @param  string  $room_id
     * @param  string  $reservation_id
     * @return void
     */
    public function destroy(Request $request, string $room_id, string $reservation_id): void
    {
        $room = $this->getRoom($request, \Auth::user(), $room_id);
        $reservation = $this->getReservationFromRoom($request, $room, \Auth::user(), $reservation_id, 'manage');

        if ($reservation->delete()) {
            abort(204);
        } else {
            abort(500, 'Impossible de supprimer la réservation');
        }
    }
}
