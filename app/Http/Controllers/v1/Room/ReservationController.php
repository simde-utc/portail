<?php

namespace App\Http\Controllers\v1\Room;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasReservations;
use App\Traits\Controller\v1\HasCreatorsAndOwnersAndValidators;
use App\Traits\Controller\v1\HasValidators;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\ReservationType;


/**
 * @resource Reservation
 *
 * Gestion des salles
 */
class ReservationController extends Controller
{
	use HasReservations, HasCreatorsAndOwnersAndValidators;

	public function __construct() {
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

	protected function checkValidations(Request $request, $room_id, $owner, $inputs) {
		if ($inputs['full_day'] ?? false) {
			$inputs['begin_at'] = Carbon::parse($inputs['begin_at'])->toDateString();
			$inputs['end_at'] = Carbon::parse($inputs['end_at'])->toDateString();
		}

		// On va maintenant voir si le type de réservation est auto-validée et que la durée n'est pas trop longue
		if (!$this->checkReservationPeriod($room_id, $inputs['begin_at'], $inputs['end_at'])) {
			$inputs['validated_by_id'] = null;
			$inputs['validated_by_type'] = null;
		}
		else if (isset($inputs['validated_by_type'])) {
			// Ici on vérifie si la valideur peu valider la demande de résa du demandeur
			$validator = $this->getValidatorFromOwner($request, $owner, 'reservation', 'réservation', 'create');

			// Maintenant on vérifie qu'on a le droit de valider pour une résa dans une salle appartenant à qq'un
			if (!$room->owned_by->isReservationValidableBy($validator))
				abort(403, 'Vous n\'avez pas le droit de valider cette réservation');

			$inputs['validated_by_id'] = $validator->id;
			$inputs['validated_by_type'] = get_class($validator);
		}
		else if (!ReservationType::find($inputs['reservation_type_id'])->need_validation) {
			$inputs['validated_by_id'] = $inputs['owned_by_id'];
			$inputs['validated_by_type'] = $inputs['owned_by_type'];
		}

		return $inputs;
	}

	/**
	 * List Visibilities
	 * @return JsonResponse
	 */

	public function index(Request $request, string $room_id): JsonResponse {
		$room = $this->getRoom($request, \Auth::user(), $room_id);

		$reservations = $room->reservations()->getSelection()->filter(function ($reservation) {
			return !\Auth::id() || $this->isVisible($reservation, \Auth::id());
		})->values()->map(function ($reservation) {
			return $reservation->hideData();
		});

		return response()->json($reservations, 200);
	}

	/**
	 * Create Reservation
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request, string $room_id): JsonResponse {
		$room = $this->getRoom($request, \Auth::user(), $room_id);
		$inputs = $request->all();

		$owner = $this->getOwner($request, 'reservation', 'réservation', 'create');
		$creator = $this->getCreatorFromOwner($request, $owner, 'reservation', 'réservation', 'create');
		// On vérifie que celui qui veut faire la réservation à le droit dans cette salle
		if (!$room->owned_by->isRoomReservableBy($owner))
			abort(403, 'Vous n\'être pas autorisé à réserver cette salle');

		$inputs['created_by_id'] = $creator->id;
		$inputs['created_by_type'] = get_class($creator);
		$inputs['owned_by_id'] = $owner->id;
		$inputs['owned_by_type'] = get_class($owner);

		$inputs = $this->checkValidations($request, $room_id, $owner, $inputs);

		$calendar = $room->calendar;

		// WARNING: L'évènement appartient bien sûr à celui qui possède le calendrier (pour éviter en fait que les gens modifient eux-même l'event)
		$event = Event::create([
			'name' => $inputs['name'] ?? ReservationType::find($inputs['reservation_type_id']),
			'begin_at' => $inputs['begin_at'],
			'end_at' => $inputs['end_at'],
			'full_day' => $inputs['full_day'] ?? false,
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
		}
		else
			abort(500, 'Impossible de créer la réservation');
	}

	/**
	 * Show Reservation
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, string $room_id, $id): JsonResponse {
		$room = $this->getRoom($request, \Auth::user(), $room_id);
		$reservation = $this->getReservationFromRoom($request, $room, \Auth::user(), $id);

		return response()->json($reservation->hideSubData(), 200);
	}

	/**
	 * Update Reservation
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, string $room_id, string $id): JsonResponse {
		$room = $this->getRoom($request, \Auth::user(), $room_id);
		$reservation = $this->getReservationFromRoom($request, $room, \Auth::user(), $id, 'edit');
		$inputs = $request->all();

		if (isset($inputs['validated_by_type'])) {
			// Ici on vérifie si la valideur peu valider la demande de résa du demandeur
			$validator = $this->getValidatorFromOwner($request, $reservation->owned_by, 'reservation', 'réservation', 'create');

			// Maintenant on vérifie qu'on a le droit de valider pour une résa dans une salle appartenant à qq'un
			if (!$room->owned_by->isReservationValidableBy($validator))
				abort(403, 'Vous n\'avez pas le droit de valider cette réservation');
		}

		$inputs = $this->checkValidations($request, $room_id, $reservation->owned_by, $inputs);

		if ($reservation->event->update($inputs) && $reservation->update($inputs))
			return response()->json($reservation->hideSubData(), 201);
		else
			abort(500, 'Impossible de modifier la réservation');
	}

	/**
	 * Delete Reservation
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, string $room_id, string $id): JsonResponse {
		$room = $this->getRoom($request, \Auth::user(), $room_id);
		$reservation = $this->getReservationFromRoom($request, $room, \Auth::user(), $id, 'manage');

		if ($reservation->delete())
			abort(204);
		else
			abort(500, 'Impossible de supprimer la réservation');
	}
}
