<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerRequest;
use App\Models\Partner;
use Illuminate\Http\Request;

/**
 * @resource Partner
 *
 * Gestion des partenaires
 */
class PartnerController extends Controller
{
	/**
	 * List Partners
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$partners = Partner::all();

		if ($partners)
			return response()->json($partners, 200);
		else
			return response()->json(['message' => 'Erreur'], 500);
	}

	/**
	 * Create Partner
	 *
	 * @param  \Illuminate\Http\PartnerRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(PartnerRequest $request)
	{
		$partner = Partner::create($request->input());

		if ($partner)
			return response()->json($partner, 200);
		else
			return response()->json(['message' => 'Le partenaire n\'a pas pu être créé'], 500);
	}

	/**
	 * Show Partner
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$partner = Partner::find($id);

		if ($partner)
			return response()->json($partner,200);
		else
			return response()->json(['message' => 'Le partenaire demandé n\'a pas été trouvé'], 404);
	}

	/**
	 * Update Partner
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(PartnerRequest $request, $id)
	{
		$partner = Partner::find($id);

		if ($partner) {
			if ($partner->update($request->input())) {
				if (Partner::where('name', $request->input('name'))->get()->first() && ($partner->name != $request->input('name')))
					return response()->json('Ce partenaire existe déjà, conflit', 409);
				else
					return response()->json($partner,200);
			}
			return response()->json(['message' => 'Erreur pendant la mise à jour du partenaire'], 500);
		}
		return response()->json(['message' => 'Le partenaire demandé n\'a pas été trouvé'], 404);
	}

	/**
	 * Delete Partner
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$partner = Partner::find($id);

		if ($partner) {
			if ($partner->delete())
				return response()->json(['message' => 'Le partenaire a bien été supprimé'], 200);
			else
				return response()->json(['message' => 'Erreur lors de la suppression du partenaire'], 500);
		}
		else
			return response()->json(['message' => 'Le partenaire demandé n\'a pas été trouvé'], 404);
	}
}
