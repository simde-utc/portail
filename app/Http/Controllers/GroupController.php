<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Models\Group;
use App\Http\Requests\GroupRequest;
use App\Models\Visibility;
use App\Traits\HasVisibility;
use App\Exceptions\PortailException;

/**
 * Gestion des groupes utilisateurs
 *
 * @resource Group
 */
class GroupController extends Controller
{
	use HasVisibility;

	/**
	 * Scopes Group
	 *
	 * Les Scopes requis pour manipuler les Groups
	 */
	public function __construct() {
		$this->middleware(
			\Scopes::matchOne(
				['user-get-groups-enabled', 'user-get-groups-disabled'],
				['client-get-groups-enabled', 'client-get-groups-disabled']
			),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['user-manage-groups']
			),
			['only' => ['store', 'update', 'destroy']]
		);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function index(Request $request): JsonResponse {
		// On inclue les relations et on les formattent.
		$groups = Group::with([
			                      'owner',
			                      'visibility',
		                      ])->get();

		if (\Auth::id()) {
			$groups = $this->hide($groups, true, function ($group) use ($request) {
				$this->hideUserData($request, $group->owner);

				return $group;
			});
		}

		return response()->json($groups, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param GroupRequest $request
	 * @return JsonResponse
	 */
	public function store(GroupRequest $request): JsonResponse {
		$group = new Group;
		$group->user_id = \Auth::id();
		$group->name = $request->name;
		$group->icon = $request->icon;
		$group->visibility_id = $request->visibility_id ?? Visibility::findByType('private')->id;

		if ($group->save()) { // Le créateur du groupe devient automatiquement admin et membre de son groupe
			// Les ids des membres à ajouter seront passé dans la requête.
			// ids est un array de user ids.
			// TODO: Envoyer un mail d'invitation dans le groupe

			try {
				$group->assignMembers($request->input('member_ids', []), [
					'semester_id' => $request->input('semester_id', 0),
				]);
			} catch (PortailException $e) {
				return response()->json(["message" => $e->getMessage()], 400);
			}

			$group = Group::with([
				                     'owner',
				                     'visibility',
			                     ])->find($group->id);

			$this->hideUserData($request, $group->owner);

			return response()->json($group, 201);
		}
		else
			abort(500, 'Impossible de créer le groupe');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, $id): JsonResponse {
		// On inclue les relations et on les formattent.
		$group = Group::with([
			                     'owner',
			                     'visibility',
		                     ])->find($id);

		if (\Auth::id()) {
			$group = $this->hide($group, false, function ($group) use ($request) {
				$this->hideUserData($request, $group->owner);

				return $group;
			});
		}

		if ($group)
			return response()->json($group, 200);
		else
			abort(404, "Groupe non trouvé");
	}

	/**
	 * Update Group
	 *
	 * @param GroupRequest $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(GroupRequest $request, $id): JsonResponse {
		$group = Group::find($id);

		if (!$group)
			abort(404, "Groupe non trouvé");

		if ($request->filled('user_id'))
			$group->user_id = $request->input('user_id');

		if ($request->filled('name'))
			$group->name = $request->input('name');

		if ($request->filled('icon'))
			$group->icon = $request->input('icon');

		if ($request->filled('visibility_id'))
			$group->visibility_id = $request->input('visibility_id');

		if ($group->save()) {
			if ($request->filled('member_ids')) {
				try {
					$group->syncMembers(array_merge($request->member_ids, [\Auth::id()]), [
						'semester_id' => $request->input('semester_id', 0),
						'removed_by'  => $group->user_id,
					], \Auth::id());
				} catch (PortailException $e) {
					return response()->json(["message" => $e->getMessage()], 400);
				}
			}

			$group = Group::with([
				                     'owner',
				                     'visibility',
			                     ])->find($id);

			$this->hideUserData($request, $group->owner);

			return response()->json($group, 200);
		}
		else
			abort(500, 'Impossible de modifier le groupe');
	}

	/**
	 * Delete Group
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy($id): JsonResponse {
		$group = Group::find($id);

		if (!$group || !$group->delete())
			abort(404, "Groupe non trouvé");
		else
			abort(204);
	}
}
