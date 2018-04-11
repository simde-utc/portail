<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Group;
use App\Http\Requests\GroupRequest;
use App\Services\Visible\Visible;
use App\Models\Visibility;

class GroupController extends Controller
{
    public function __construct() {
        $this->middleware(\Scopes::matchOne(['user-manage-groups']), ['only' => ['store', 'update', 'destroy']]);
        $this->middleware(\Scopes::matchOne(['client-get-groups-enabled', 'client-get-groups-disabled', 'user-get-groups-enabled', 'user-get-groups-disabled']), ['only' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $groups = Group::where('is_active', $request->input('active', 1) != 0)->get();

		return response()->json($request->user() ? Visible::with($groups, $request->user()->id) : $groups, 200);
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
        $group->user_id = $request->user()->id;
        $group->name = $request->name;
        $group->icon = $request->icon;
        $group->visibility_id = $request->visibility_id ?? Visibility::where('type', 'owner')->first()->id;
        $group->is_active = $request->is_active;

        // Les ids des membres à ajouter seront passé dans la requête.
        // ids est un array de user ids.
        if ($request->has('member_ids'))
            $group->members()->attach($request->input('member_ids', []));

        if ($group->save())
            return response()->json($group, 201);
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
        $group = Group::find($id);

        if ($group)
            return response()->json($request->user() ? Visible::hide($group, $request->user()->id) : $group, 200);
        else
            return response()->json(["message" => "Impossible de trouver le groupe"], 404);
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

		if ($request->has('is_active'))
			$group->is_active = $request->input('is_active', true);

        // Les ids de tout les membres (actuels et anciens) seront passés dans la requête.
        // ids est un array de user ids.
        if ($request->has('member_ids'))
            $group->members()->sync($request->member_ids);

        if ($group->save())
            return response()->json($group, 200);
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

        $group->members()->detach();

        $group->delete();

        return response()->json(["message" => "Groupe supprimé"], 204);
    }
}
