<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asso;
use App\Models\Event;
use App\Models\Calendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AbstractCalendarController;
use App\Services\Visible\Visible;
use App\Interfaces\CanHaveEvents;
use App\Traits\HasVisibility;

/**
 * @resource Event
 *
 * Gestion des évènements
 */
class EventController extends AbstractCalendarController
{
	public function __construct() {
		parent::__construct();

		$this->middleware(
			\Scopes::matchOne(array_merge(
				$this->populateScopes('user-get-events', 'created'),
				$this->populateScopes('user-get-events', 'owned-client'),
				$this->populateScopes('user-get-events', 'owned-asso')
			), array_merge(
				$this->populateScopes('client-get-events', 'created'),
				$this->populateScopes('client-get-events', 'owned-client'),
				$this->populateScopes('client-get-events', 'owned-asso')
			)),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne(array_merge(
				$this->populateScopes('user-set-events', 'owned-client'),
				$this->populateScopes('user-set-events', 'owned-asso'),
				$this->populateScopes('user-create-events', 'owned'),
				$this->populateScopes('user-create-events', 'created')
			), array_merge(
				$this->populateScopes('client-set-events', 'owned-client'),
				$this->populateScopes('client-set-events', 'owned-asso'),
				$this->populateScopes('client-create-events', 'owned'),
				$this->populateScopes('client-create-events', 'created')
			)),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOne(array_merge(
				$this->populateScopes('user-set-events', 'created'),
				$this->populateScopes('user-set-events', 'owned-client'),
				$this->populateScopes('user-set-events', 'owned-asso')
			), array_merge(
				$this->populateScopes('client-set-events', 'created'),
				$this->populateScopes('client-set-events', 'owned-client'),
				$this->populateScopes('client-set-events', 'owned-asso')
			)),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOne(array_merge(
				$this->populateScopes('user-manage-events', 'created'),
				$this->populateScopes('user-manage-events', 'owned-client'),
				$this->populateScopes('user-manage-events', 'owned-asso')
			), array_merge(
				$this->populateScopes('client-manage-events', 'created'),
				$this->populateScopes('client-manage-events', 'owned-client'),
				$this->populateScopes('client-manage-events', 'owned-asso')
			)),
			['only' => ['destroy']]
		);
	}

	public function getCreaterOrOwner(Request $request, string $verb = 'create', string $type = 'created') {
		$scopeHead = \Scopes::isUserToken($request) ? 'user' : 'client';
		$scope = $scopeHead.'-'.$verb.'-events-'.$request->input($type.'_by_type',\Scopes::isClientToken($request) ? 'client' : 'user').'s-'.$type;

		if ($type === 'owned')
			$scope = array_keys(\Scopes::getRelatives($scopeHead.'-'.$verb.'-events-'.$request->input($type.'_by_type').'s-'.$type));

		if (!\Scopes::hasOne($request, $scope))
			abort(403, 'Il ne vous est pas autorisé de créer de calendriers');

		if ($request->filled($type.'_by_type')) {
			if ($request->filled($type.'_by_id')) {
				$createrOrOwner = resolve($this->types[$request->input($type.'_by_type')])->find($request->input($type.'_by_id'));

				if (\Auth::id() && !$createrOrOwner->isCalendarManageableBy(\Auth::id()))
					abort(403, 'L\'utilisateur n\'a pas les droits de création');
			}
			else if ($request->input($type.'_by_type', 'client') === 'client')
				$createrOrOwner = Client::find(\Scopes::getClient($request)->id);
			else if ($request->input($type.'_by_type', 'client') === 'asso')
				$createrOrOwner = \Scopes::getClient($request)->asso;
		}

		if (!isset($createrOrOwner))
			$createrOrOwner = \Scopes::isClientToken($request) ? \Scopes::getClient($request) : \Auth::user();

		if (!($createrOrOwner instanceof CanHaveCalendars))
			abort(400, 'La personne créatrice/possédeur doit au moins pouvoir avoir un calendrier');

		return $createrOrOwner;
	}

	public function isPrivate($user_id, $model = null) {
		if ($model === null)
			return false;

		// Si c'est privée, uniquement les personnes ayant un calendrier contenant cet event peuvent le voir
		$user = User::find($user_id);
		$calendar_ids = $user->calendars->pluck('id')->merge($user->followedCalendars->pluck('id'));
		$event_calendar_ids = $model->calendars->pluck('id');

		$model->makeHidden('calendars');

		if (count($calendar_ids->intersect($event_calendar_ids)) !== 0)
			return true;

		return $model->owned_by->isEventAccessibleBy($user_id);
    }

	/**
	 * List Events
	 *
	 * @return JsonResponse
	 */
	public function index(Request $request): JsonResponse {
		$events = Event::with(['owned_by', 'created_by', 'visibility', 'details', 'location'])->get()->filter(function ($event) use ($request) {
			return $this->tokenCanSee($request, $event, 'get', 'events');
		})->values()->map(function ($event) use ($request) {
			return $this->hideEventData($request, $event);
		});

		return response()->json($events, 200);
	}

	/**
	 * Create Event
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request): JsonResponse {
		$inputs = $request->all();

		if (\Scopes::isUserToken($request))
			$creater = \Auth::user();
		else {
			// A faire au choix
		}

		$owner = \Auth::id();

		if (!($owner instanceof CanHaveEvents))
			abort(400, 'Le owner doit au moins pouvoir avoir un évènenement');
		if (!($owner instanceof CanHaveEvents))
			abort(400, 'Le owner doit au moins pouvoir avoir un évènenement');

		$inputs['created_by_id'] = $creater->id;
		$inputs['created_by_type'] = get_class($creater);
		$inputs['owned_by_id'] = $owner->id;
		$inputs['owned_by_type'] = get_class($owner);

		$calendar = Calendar::find($inputs['calendar_id']);

		if (!$calendar->owned_by->isCalendarManageableBy(\Auth::id()))
			abort(403, 'Vous n\'avez pas les droits suffisants pour ajouter cet évènenement à ce calendrier');

		$event = Event::create($inputs);

		if ($event) {
			$event->calendars()->attach($calendar);

			return response()->json($event, 201);
		}
		else
			return response()->json(['message' => 'Impossible de créer le évènenement'], 500);

	}

	/**
	 * Show Event
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, int $id): JsonResponse {
		$event = $this->getEvent($request, $id);
		$event = $this->hideEventData($request, $event);

		return response()->json($event, 200);
	}

	/**
	 * Update Event
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, $id): JsonResponse {
		$event = $this->getEvent($request, $id, true);

		if ($event->update($request->input()))
			return response()->json($this->hideEventData($event), 200);
		else
			abort(500, 'Impossible de modifier le évènenement');
	}

	/**
	 * Delete Event
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, int $id): JsonResponse {
		$event = $this->getEvent($request, $id, true);
		$event->softDelete();

		abort(204);
	}
}
