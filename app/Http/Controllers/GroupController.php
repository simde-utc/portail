<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Group;
use App\Http\Requests\GroupRequest;
use App\Services\Visible\Visible;
use App\Models\Visibility;

/**
 * @resource Group
 *
 * Gestion des groupe d'utilisateurs
 */
class GroupController extends Controller
{
	/**
	 * Scopes Group
	 *
	 * Les Scopes requis pour manipuler les Groups
	 */
	public function __construct() {
		$this->middleware(\Scopes::matchOne(
			['client-get-groups-enabled', 'client-get-groups-disabled', 'user-get-groups-enabled', 'user-get-groups-disabled']), 
			['only' => ['index', 'show']]
		);
		$this->middleware(\Scopes::matchOne(
			['user-manage-groups']),
			['only' => ['store', 'update', 'destroy']]
		);
	}

	/**
	 * List Groups
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		// On inclue les relations et on les formattent.
		$groups = Group::with([
			'owner:id,lastname,firstname',
			'visibility',
			'members:id,lastname,firstname'
		])->where('is_active', 1)->get();

		return response()->json($request->user() ? Visible::with($groups, $request->user()->id) : $groups, 200);
	}

	/**
	 * Create Group
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(GroupRequest $request)
	{
		$group = new Group;
		$group->user_id = 1; // PROD : $request->user()->id;
		$group->name = $request->name;
		$group->icon = $request->icon;
		$group->visibility_id = $request->visibility_id ?? Visibility::where('type', 'owner')->first()->id;
		$group->is_active = $request->is_active;

		if ($group->save()) {
			// Owner est automatiquement membre du groupe.
			$group->members()->attach(1); //PROD : $request->user()->id);

			// Les ids des membres à ajouter seront passé dans la requête.
			// ids est un array de user ids.
			if ($request->has('member_ids'))
				$group->members()->attach($request->input('member_ids', []));

			return response()->json($group, 201);
		}
		else
			return response()->json(['message' => 'Impossible de créer le groupe'], 500);
	}

	/**
	 * Show Group
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id)
	{
		// On inclue les relations et on les formattent.
		$group = Group::with([
			'owner:id,firstname,lastname',
			'visibility',
			'members:id,firstname,lastname'])
			->find($id);

		if ($group)
			return response()->json($request->user() ? Visible::hide($group, $request->user()->id) : $group, 200);
		else
			abort(404, 'Groupe non trouvé');
	}

	/**
	 * Update Group
	 *
	 * @param  \Illuminate\Http\Request  $request
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

		if ($request->has('is_active'))
			$group->is_active = $request->input('is_active', true);

		// En update on enleve les ids précedents donc on sync.
		$group->members()->sync(1); // TODO 1 ? PROD : $request->user()->id);

		// Pas de sync() vu qu'on veut garder owner id.
		// Les ids de tous les membres (actuels et anciens) seront passés dans la requête.
		if ($request->has('member_ids'))
			$group->members()->syncWithoutDetaching($request->member_ids);

		if ($group->save())
			return response()->json($group, 200);
		else
			return response()->json(['message' => 'Impossible de modifier le groupe'], 500);
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
			return response()->json(['message' => 'Impossible de trouver le groupe'], 404);

		$group->members()->detach();

		$group->delete();

		return response()->json(['message' => 'Groupe supprimé'], 204);
	}
}
