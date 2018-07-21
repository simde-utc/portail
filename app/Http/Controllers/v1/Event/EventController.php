<?php

namespace App\Http\Controllers\v1\Event;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasEvents;
use App\Models\User;
use App\Models\Asso;
use App\Models\Event;
use App\Models\Calendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Visible\Visible;
use App\Interfaces\CanHaveEvents;
use App\Traits\HasVisibility;

/**
 * @resource Event
 *
 * Gestion des évènements
 */
class EventController extends Controller
{
	use HasEvents;

	public function __construct() {
		parent::__construct();

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

	public function getCreaterOrOwner(Request $request, string $verb = 'create', string $type = 'created') {
		$scopeHead = \Scopes::getTokenType($request);
		$scope = $scopeHead.'-'.$verb.'-events-'.$request->input($type.'_by_type', $scopeHead.'s-'.$type);

		if ($type === 'owned')
			$scope = array_keys(\Scopes::getRelatives($scopeHead.'-'.$verb.'-events-'.$request->input($type.'_by_type').'s-'.$type));

		if (!\Scopes::hasOne($request, $scope))
			abort(403, 'Il ne vous est pas autorisé de créer de évènements');

		if ($request->filled($type.'_by_type')) {
			if ($request->filled($type.'_by_id')) {
				$createrOrOwner = resolve($this->types[$request->input($type.'_by_type')])->find($request->input($type.'_by_id'));

				if (\Auth::id() && !$createrOrOwner->isEventManageableBy(\Auth::id()))
					abort(403, 'L\'utilisateur n\'a pas les droits de création');
			}
			else if ($request->input($type.'_by_type', 'client') === 'client')
				$createrOrOwner = \Scopes::getClient($request);
			else if ($request->input($type.'_by_type', 'client') === 'asso')
				$createrOrOwner = \Scopes::getClient($request)->asso;
		}

		if (!isset($createrOrOwner))
			$createrOrOwner = \Scopes::isClientToken($request) ? \Scopes::getClient($request) : \Auth::user();

		if (!($createrOrOwner instanceof CanHaveEvents))
			abort(400, 'La personne créatrice/possédeur doit au moins pouvoir avoir un calendrier');

		return $createrOrOwner;
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
			return $event->hideData();
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

		$owner = $this->getCreaterOrOwner($request, 'create', 'owned');

		if ($request->input('created_by_type') === 'client'
			&& $request->input('created_by_id', \Scopes::getClient($request)->id) === \Scopes::getClient($request)->id
			&& \Scopes::hasOne($request, (\Scopes::isClientToken($request) ? 'client' : 'user').'-create-calendars-'.$this->classToType(get_class($owner)).'s-owned-client'))
			$creater = \Scopes::getClient($request)->id;
		else if ($request->input('created_by_type') === 'asso'
			&& $request->input('created_by_id', \Scopes::getClient($request)->asso->id) === \Scopes::getClient($request)->asso->id
			&& \Scopes::hasOne($request, (\Scopes::isClientToken($request) ? 'client' : 'user').'-create-calendars-'.$this->classToType(get_class($owner)).'s-owned-client'))
			$creater = \Scopes::getClient($request)->asso;
		else
			$creater = $this->getCreaterOrOwner($request, 'create', 'created');

		$inputs['created_by_id'] = $creater->id;
		$inputs['created_by_type'] = get_class($creater);
		$inputs['owned_by_id'] = $owner->id;
		$inputs['owned_by_type'] = get_class($owner);

		$calendar = $this->getCalendar($request, \Auth::user(), Calendar::find($inputs['calendar_id']), 'edit');

		if (!$calendar->owned_by->isEventManageableBy(\Auth::id()))
			abort(403, 'Vous n\'avez pas les droits suffisants pour ajouter cet évènenement à ce calendrier');

		$event = Event::create($inputs);

		if ($event) {
			$event = $this->getEvent($request, \Auth::user(), $event->id);

			return response()->json($event->hideData(), 201);
		}
		else
			return response()->json(['message' => 'Impossible de créer l\'évènenement'], 500);
	}

	/**
	 * Show Event
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, int $id): JsonResponse {
		$event = $this->getEvent($request, \Auth::user(), $id);

		return response()->json($event->hideData(), 200);
	}

	/**
	 * Update Event
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, $id): JsonResponse {
		$event = $this->getEvent($request, \Auth::user(), $id, 'set', true);
		$inputs = $request->all();

		if ($request->filled('owned_by_type')) {
			$owner = $this->getCreaterOrOwner($request, 'set', 'owned');

			$inputs['owned_by_id'] = $owner->id;
			$inputs['owned_by_type'] = get_class($owner);
		}

		if ($event->update($inputs))
			return response()->json($event->hideData(), 200);
		else
			abort(500, 'Impossible de modifier le calendrier');
	}

	/**
	 * Delete Event
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, int $id): JsonResponse {
		$event = $this->getEvent($request, \Auth::user(), $id, true);
		$event->softDelete();

		abort(204);
	}
}
