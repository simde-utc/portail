<?php

namespace App\Http\Controllers\v1\Reservation;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasReservations;
use App\Traits\Controller\v1\HasCreatorsAndOwnersAndValidators;
use App\Traits\Controller\v1\HasValidators;
use App\Http\Requests\ReservationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Room;
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
			\Scopes::matchOne('user-get-reservations', 'client-get-reservations'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne('user-create-reservations', 'client-create-reservations'),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOne('user-edit-reservations', 'client-edit-reservations'),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOne('user-remove-reservations', 'client-remove-reservations'),
			['only' => ['destroy']]
		);
	}

	/**
	 * List Visibilities
	 * @return JsonResponse
	 */

	public function index(): JsonResponse {
		$reservations = Reservation::getSelection()->filter(function ($reservation) {
			return !\Auth::id() || $this->isVisible($reservation, \Auth::id());
		})->values()->map(function ($reservation) {
			return $reservation->hideData();
		});

		return response()->json($reservations, 200);
	}

	/**
	 * Create Reservation
	 *
	 * @param ReservationRequest $request
	 * @return JsonResponse
	 */
	public function store(ReservationRequest $request): JsonResponse {
		$inputs = $request->all();

		$owner = $this->getOwner($request, 'reservation', 'réservation', 'create');
		$creator = $this->getCreatorFromOwner($request, $owner, 'reservation', 'réservation', 'create');

		// On vérifie que celui qui veut faire la réservation à le droit dans cette salle
		if (!Room::find($inputs['room_id'])->owned_by->isRoomReservableBy($owner))
			abort(403, 'Vous n\'être pas autorisé à réserver cette salle');

		$inputs['created_by_id'] = $creator->id;
		$inputs['created_by_type'] = get_class($creator);
		$inputs['owned_by_id'] = $owner->id;
		$inputs['owned_by_type'] = get_class($owner);

		// On va maintenant voir si le type de réservation est auto-validée qu'on a pas de valideur
		if (isset($inputs['validated_by_type']) {
			$validator = $this->getValidatorFromOwner($request, $owner, 'reservation', 'réservation', 'create');

			$inputs['validated_by_id'] = $validator->id;
			$inputs['validated_by_type'] = get_class($validator);
		}
		else if (!ReservationType::find($inputs['reservations_type'])->need_validation) {
			$inputs['validated_by_id'] = $inputs['owned_by_id'];
			$inputs['validated_by_type'] = $inputs['owned_by_type'];
		}

		Reservation::create($inputs);

		if ($reservation) {
			$reservation = $this->getReservation($request, \Auth::user(), $reservation->id);

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
	public function show($id): JsonResponse {
		$reservation = $this->getReservation($request, \Auth::user(), $id);

		return response()->json($reservation->hideSubData(), 200);
	}

	/**
	 * Update Reservation
	 *
	 * @param ReservationRequest $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(ReservationRequest $request, string $id): JsonResponse {
		$reservation = $this->getReservation($request, \Auth::user(), $id, 'edit');
		$inputs = $request->all();

		if (isset($inputs['validated_by_type']) {
			$validator = $this->getValidatorFromOwner($request, $reservation->owned_by, 'reservation', 'réservation', 'edit');

			$inputs['validated_by_id'] = $validator->id;
			$inputs['validated_by_type'] = get_class($validator);
		}

		if ($room->update($request->input()))
			return response()->json($room->hideSubData(), 201);
		else
			abort(500, 'Impossible de modifier la réservation');
	}

	/**
	 * Delete Reservation
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(string $id): JsonResponse {
		$reservation = $this->getReservation($request, \Auth::user(), $id, 'manage');

		if ($reservation->delete())
			abort(204);
		else
			abort(500, 'Impossible de supprimer la réservation');
	}
}
