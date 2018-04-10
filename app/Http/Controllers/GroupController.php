<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Group;
use App\Http\Requests\GroupRequest;
use App\Services\Visible\Visible;

class GroupController extends Controller
{
    public function __construct() {
        $this->middleware(\Scopes::matchOne(['user-manage-groups'], NULL), ['only' => ['store', 'update', 'destroy']]);
        $this->middleware(\Scopes::matchOne(NULL, ['client-get-groups']), ['only' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = Group::where('is_active', 1)->get();
        return response()->json(Visible::hide($groups), 200);
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
        $group->user_id = Auth::user()->id; // A vérifier si c'est bon vis à vis du Oauth.
        $group->name = $request->name;
        $group->icon = $request->icon;
        $group->visibility_id = $request->visibility_id;
        $group->is_active = $request->is_active;

        // Les ids des membres à ajouter seront passé dans la requête.
        // ids est un array de user ids.
        if ($request->has('ids'))
            $group->members()->attach($request->ids);

        $group->save();

        if ($group)
            return response()->json($group, 200);
        else
            return response()->json(["message" => "Impossible de créer le groupe"], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = Group::find($id);
        if ($group)
            return response()->json(Visible::hide($group), 200);
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

        $group->user_id = Auth::user()->id; // A vérifier si c'est bon vis à vis du Oauth.
        $group->name = $request->name;
        $group->icon = $request->icon;
        $group->visiblity_id = $request->visiblity_id;
        $group->is_active = $request->is_active;

        // Les ids de tout les membres (actuels et anciens) seront passés dans la requête.
        // ids est un array de user ids.
        if ($request->has('ids'))
            $group->members()->sync($request->ids);

        $group->save();

        if ($group)
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

        return response()->json(["message" => "Groupe supprimé"], 200);
    }
}
