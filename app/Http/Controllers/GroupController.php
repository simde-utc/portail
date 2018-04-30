<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Group;
use App\Http\Requests\GroupRequest;
use App\Models\Visibility;
use App\Exceptions\PortailException;
use App\Traits\HasVisibility;

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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // On inclue les relations et on les formattent.
		$groups = Group::with([
            'owner',
            'visibility',
		])->get();

		if (\Auth::id()) {
			$group = $this->hide($groups, true, function ($group) use ($request) {
				$this->hideUserData($request, $group->owner);

				return $group;
			});
		}

		return response()->json($groups, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupRequest $request)
    {
        $group = new Group;
        $group->user_id = \Auth::id();
        $group->name = $request->name;
        $group->icon = $request->icon;
        $group->visibility_id = $request->visibility_id ?? Visibility::findByType('owner')->id;

        if ($group->save()) { // Le créateur du groupe devient automatiquement admin et membre de son groupe
            // Les ids des membres à ajouter seront passé dans la requête.
            // ids est un array de user ids.
            if ($request->has('member_ids')) {
				if ($group->visibility_id === Visibility::findByType('owner')->id)
					$data = [
						'semester_id' => $request->input('semester_id', 0),
						'validated_by' => $group->user_id,
					];
				else {
					$data = [
						'semester_id' => $request->input('semester_id', 0),
					];
					// TODO: Envoyer un mail d'invitation dans le groupe
				}

				try {
					$group->assignMembers($request->input('member_ids', []), $data);
				} catch (PortailException $e) {
					return response()->json(["message" => $e->getMessage()], 400);
				}
			}

			$group = $group->with([
	            'owner',
	            'visibility',
			]);

			$this->hideUserData($request, $group->owner);

            return response()->json($group, 201);
        }
        else
            return response()->json(["message" => "Impossible de créer le groupe"], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
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
	 * @param  \Illuminate\Http\GroupRequest  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(GroupRequest $request, $id)
	{
		$group = Group::find($id);

		if (!$group)
			return response()->json(['message' => 'Impossible de trouver le groupe'], 404);

		if ($request->has('user_id'))
			$group->user_id = $request->input('user_id');

		if ($request->has('name'))
			$group->name = $request->input('name');

		if ($request->has('icon'))
			$group->icon = $request->input('icon');

		if ($request->has('visibility_id'))
			$group->visibility_id = $request->input('visibility_id');

        if ($group->save()) {
	        if ($request->has('member_ids')) {
				if ($group->visibility_id >= Visibility::findByType('owner')->id)
					$data = [
						'semester_id' => $request->input('semester_id', 0),
						'validated_by' => $group->user_id,
						'removed_by' => $group->user_id,
					];
				else {
					$data = [
						'semester_id' => $request->input('semester_id', 0),
						'removed_by' => $group->user_id,
					];
					// TODO: Envoyer un mail d'invitation dans le groupe
				}

				try {
					$group->syncMembers(array_merge($request->member_ids, [\Auth::id()]), $data, \Auth::id());
				} catch (PortailException $e) {
					return response()->json(["message" => $e->getMessage()], 400);
				}
			}

			$group = $group->with([
	            'owner',
	            'visibility',
			]);

			$this->hideUserData($request, $group->owner);

            return response()->json($group, 200);
		}
        else
            return response()->json(["message" => "Impossible de modifier le groupe"], 500);
    }

	/**
	 * Delete Group
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$group = Group::find($id);

        if (!$group)
            return response()->json(["message" => "Impossible de trouver le groupe"], 404);
		else {
			$group->delete();

			return abort(204);
		}
    }
}
