<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerRequest;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $partners = Partner::all();
        if($partners)
        	return response()->json($partners,200);
        return response()->json(['message'=>'Erreur'],500);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PartnerRequest $request)
    {
        if(Partner::where('name', $request->input('name'))->get()->first()) {
            return response()->json("Ce partenaire existe déjà, conflit", 409);
        }

        $partner = Partner::create($request->input());
        if($partner)



        	return response()->json($partner,200);
        return response()->json(['message'=>'Le partenaire n\'a pas pu être créé'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $partner = Partner::find($id);
        if($partner)
        	return response()->json($partner,200);
        return response()->json(['message'=>'Le partenaire demandé n\'a pas été trouvé'],404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PartnerRequest $request, $id)
    {
        $partner = Partner::find($id);
        if($partner){
        	$ok = $partner->update($request->input());
        	if($ok)
            {
                if(Partner::where('name', $request->input('name'))->get()->first() && ($partner->name != $request->input('name'))) 
                {    
                    return response()->json("Ce partenaire existe déjà, conflit", 409);
                }

        		return response()->json($partner,200);
        	}

            return response()->json(['message'=>'Erreur pendant la mise à jour du partenaire'],500);
        }
        return response()->json(['message'=>'Le partenaire demandé n\'a pas été trouvé'],404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $partner = Partner::find($id);
        if($partner){
			$ok = $partner->delete();
			if($ok)
				return response()->json(['message'=>'Le partenaire a bien été supprimé'],200);
			return response()->json(['message'=>'Erreur lors de la suppression du partenaire'],500);
        }
        return response()->json(['message'=>'Le partenaire demandé n\'a pas été trouvé'],404);
    }
}
