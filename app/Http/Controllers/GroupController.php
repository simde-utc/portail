<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Group;
use App\Http\Requests\GroupRequest;
use App\Services\Visible\Visible;
use App\Models\Visibility;
use App\Exceptions\PortailException;

class GroupController extends Controller
{
    public function __construct() {
		$this->middleware(\Scopes::matchOne(['client-get-groups-enabled', 'client-get-groups-disabled', 'user-get-groups-enabled', 'user-get-groups-disabled']), ['only' => ['index', 'show']]);
        $this->middleware(\Scopes::matchOne(['user-manage-groups']), ['only' => ['store', 'update', 'destroy']]);
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
            'owner:id,lastname,firstname',
            'visibility',
			'currentMembers:id,lastname,firstname'
		])->get();

		return response()->json(\Auth::user() ? Visible::with($groups, \Auth::user()->id) : $groups, 200);
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
        $group->user_id = \Auth::user();
        $group->name = $request->name;
        $group->icon = $request->icon;
        $group->visibility_id = $request->visibility_id ?? Visibility::findByType('owner')->id;

        if ($group->save()) { // Le créateur du groupe devient automatiquement admin et membre de son groupe
            // Les ids des membres à ajouter seront passé dans la requête.
            // ids est un array de user ids.
            if ($request->has('member_ids')) {
				if ($group->visibility_id === Visibility::findByType('owner')->id)
					$data = [
						'visibility_id' => $group->user_id,
					];
				else {
					$data = [];
					// TODO: Envoyer un mail d'invitation dans le groupe
				}

				try {
					$group->assignMembers($request->input('member_ids', []), $data);
				} catch (PortailException $e) {
					return response()->json(["message" => $e->getMessage()], 400);
				}
			}

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
            'owner:id,firstname,lastname',
            'visibility',
            'currentMembers:id,firstname,lastname'])
            ->find($id);

        if ($group)
            return response()->json(\Auth::user() ? Visible::hide($group, \Auth::user()->id) : $group, 200);
        else
            abort(404, "Groupe non trouvé");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GroupRequest $request, $id)
    {
        $group = Group::find($id);

        if (!$group)
            return response()->json(["message" => "Impossible de trouver le groupe"], 404);

		if ($request->has('user_id'))
			$group->user_id = $request->input('user_id');

		if ($request->has('name'))
			$group->name = $request->input('name');

		if ($request->has('icon'))
        	$group->icon = $request->input('icon');

		if ($request->has('visibility_id'))
        	$group->visibility_id = $request->input('visibility_id');

        // En update on enleve les ids précedents donc on sync.
        $group->members()->sync(\Auth::user()->id);

        if ($group->save()) {
	        if ($request->has('member_ids')) {
				if ($group->visibility_id === Visibility::findByType('owner')->id)
					$data = [
						'visibility_id' => $group->user_id,
					];
				else {
					$data = [];
					// TODO: Envoyer un mail d'invitation dans le groupe
				}

				try {
					$group->syncMembers(array_merge($request->member_ids, [\Auth::user()->id]), $data, \Auth::user()->id);
				} catch (PortailException $e) {
					return response()->json(["message" => $e->getMessage()], 400);
				}
			}

			return response()->json($group, 200);
		}
        else
            return response()->json(["message" => "Impossible de modifier le groupe"], 500);
    }

    /**
     * Remove the specified resource from storage.
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

			return response()->json(["message" => "Groupe supprimé"], 204);
		}
    }
}
