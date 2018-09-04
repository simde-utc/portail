<?php

namespace App\Http\Controllers\v1\Reservation;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasReservations;
use App\Http\Requests\ReservationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Reservation;


/**
 * @resource Reservation
 *
 * Gestion des salles
 */
class ReservationController extends Controller
{
	use HasReservations;

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
			return !\Auth::id() || $this->isVisibile($reservation, \Auth::id());
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
		$reservation = Reservation::create($request->all());

		return response()->json($reservation->hideSubData(), 200);
	}

	/**
	 * Show Reservation
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show($id): JsonResponse {
		$reservation = $this->getReservation($id);

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
		$reservation = $this->getReservation($id);

		if ($reservation->update($request->input()))
			return response()->json($reservation->hideSubData(), 201);
		else
			abort(500, 'Impossible de modifier la salle');
	}

	/**
	 * Delete Reservation
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(string $id): JsonResponse {
		$reservation = $this->getReservation($id);

		if ($reservation->delete())
			abort(204);
		else
			abort(500, 'Impossible de supprimer la salle');
	}
}
