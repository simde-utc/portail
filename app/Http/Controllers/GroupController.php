<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Group;
use App\Http\Requests\GroupRequest;
use App\Services\Visible\Visible;

class GroupController extends Controller
{
    public function __construct() {
        // $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // TODO: Add visiblity !

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
        $group = Group::create($request->input());

        // Members user id will be passed to request.
        $group->members()->attach($request->ids);

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
        // TODO: Add visiblity !

        $group = Group::find($id);
        if ($group)
            return response()->json($group, 200);
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
        
        // Members user id will be passed to request.
        // Sync erases all previous associations and replaces them with the new one.
        $group->members()->sync($request->ids);

        $group = Group::update($request->input());
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

        $group->members()->detach();

        $group->delete();

        return response()->json(["message" => "Groupe supprimé"], 200);
    }
}
