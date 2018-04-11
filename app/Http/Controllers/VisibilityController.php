<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisibilityRequest;
use Illuminate\Http\Request;
use App\Models\Visibility;


class VisibilityController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

     public function index()
    {
        $visibilities = Visibility::get();
        return response()->json($visibilities, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(VisibilityRequest $request)
    {
        $visibility = Visibility::create($request->all());

        if($visibility)
        {
            
            return response()->json($visibility, 200);
        }
        else
            return response()->json(["message" => "Impossible de créer la visibilité"], 500);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $visibility = Visibility::find($id);

        if($visibility)
            return response()->json($visibility, 200);
        else
            return response()->json(["message" => "Impossible de trouver la visibilité"], 500);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VisibilityRequest $request, $id)
    {
        $visibility = Visibility::find($id);



        if($visibility){

            $ok = $visibility->update($request->input());
    
            if($ok)
                return response()->json($visibility, 201);

            return response()->json(['message'=>'An error ocured'],500);
 
    
            
        }
        
        return response()->json(["message" => "Impossible de trouver la  visibilité"], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $visibility = Visibility::find($id);

        if ($visibility)
        {
            $visibility->delete();
            return response()->json([], 200);
        }
        else
            return response()->json(["message" => "Impossible de trouver la visibilité"], 500);
    }
}
