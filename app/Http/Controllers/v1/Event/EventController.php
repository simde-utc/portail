<?php
/**
 * Gère les évènements.
 *
 * @author Josselin Pennors <josselin.pennors@hotmail.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Event;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasEvents;
use App\Traits\Controller\v1\HasCreatorsAndOwners;
use App\Models\User;
use App\Models\Asso;
use App\Models\Event;
use App\Models\Calendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Visible\Visible;
use App\Interfaces\Model\CanHaveEvents;
use App\Traits\HasVisibility;

class EventController extends Controller
{
	use HasEvents, HasCreatorsAndOwners;

	/**
	 * Nécessité de gérer les événements.
	 */
	public function __construct() {
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-get-events', 'client-get-events'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-create-events', 'client-create-events'),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-set-events', 'client-set-events'),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-manage-events', 'client-manage-events'),
			['only' => ['destroy']]
		);
	}

	/**
     * Liste les événements.
     *
     * @param Request $request
     * @return JsonResponse
     */
	public function index(Request $request): JsonResponse {
		$events = Event::getSelection()->filter(function ($event) use ($request) {
			return $this->tokenCanSee($request, $event, 'get', 'events') && (!\Auth::id() || $this->isVisible($event, \Auth::id()) || $this->isEventFollowed($request, $event, \Auth::id()));
		})->values()->map(function ($event) use ($request) {
			return $event->hideData();
		});

		return response()->json($events, 200);
	}

	/**
     * Crée un événement.
     *
     * @param Request $request
     * @return JsonResponse
     */
	public function store(Request $request): JsonResponse {
		$inputs = $request->all();

		$calendar = $this->getCalendar($request, \Auth::user(), Calendar::find($inputs['calendar_id']), 'edit');

		if (!$calendar->owned_by->isCalendarManageableBy(\Auth::id()))
			abort(403, 'Vous n\'avez pas les droits suffisants pour ajouter cet évènenement à ce calendrier');

		$owner = $this->getOwner($request, 'event', 'évènement', 'create');
		$creator = $this->getCreatorFromOwner($request, $owner, 'event', 'évènement', 'create');

		$inputs['created_by_id'] = $creator->id;
		$inputs['created_by_type'] = get_class($creator);
		$inputs['owned_by_id'] = $owner->id;
		$inputs['owned_by_type'] = get_class($owner);

		$event = Event::create($inputs);

		if ($event) {
			$event = $this->getEvent($request, \Auth::user(), $event->id);

			return response()->json($event->hideSubData(), 201);
		}
		else
			return response()->json(['message' => 'Impossible de créer l\'évènenement'], 500);
	}

	/**
     * Montre un événement.
     *
     * @param Request 	$request
     * @param string	$id
     * @return JsonResponse
     */
	public function show(Request $request, string $id): JsonResponse {
		$event = $this->getEvent($request, \Auth::user(), $id);

		return response()->json($event->hideSubData(), 200);
	}

	/**
     * Met à jour un événement.
     *
     * @param Request 	$request
     * @param string	$id
     * @return JsonResponse
     */
	public function update(Request $request, string $id): JsonResponse {
		$event = $this->getEvent($request, \Auth::user(), $id, 'set');
		$inputs = $request->all();

		if ($request->filled('owned_by_type')) {
			$owner = $this->getOwner($request, 'event', 'évènement', 'edit');

			$inputs['owned_by_id'] = $owner->id;
			$inputs['owned_by_type'] = get_class($owner);
		}

		if ($event->update($inputs))
			return response()->json($event->hideSubData(), 200);
		else
			abort(500, 'Impossible de modifier le calendrier');
	}

	/**
     * Supprime un événement.
     *
     * @param Request 	$request
     * @param string	$id
     * @return void
     */
	public function destroy(Request $request, string $id): JsonResponse {
		$event = $this->getEvent($request, \Auth::user(), $id);
		$event->softDelete();

		abort(204);
	}
}
