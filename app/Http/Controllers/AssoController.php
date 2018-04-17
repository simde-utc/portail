<?php

namespace App\Http\Controllers;

use App\Models\Asso;
use App\Http\Requests\AssoRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @resource Association
 *
 * Gestion des associations
 */
class AssoController extends Controller
{
	public function __construct() {
		// $this->middleware('auth:api', ['except' => ['index', 'show']]);
	}

	/**
	 * List Associations
	 *
	 * Retourne la liste des associations
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		return response()->json(Asso::get(), 200);
	}

	/**
	 * Store Association
	 *
	 * Créer une Association
	 * @param  \Illuminate\Http\AssoRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(AssoRequest $request) {
		$asso = Asso::create($request->input());

		if ($asso)
			return response()->json($asso, 201);
		else
			return response()->json(['message' => 'Impossible de créer l\'association'], 500);
	}

	/**
	 * Show Association
	 *
	 * Retourne l'association si elle existe
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		$asso = Asso::find($id);

		if ($asso)
			return response()->json($asso, 200);
		else
			return response()->json(['message' => 'L\'asso demandée n\'a pas pu être trouvée'], 404);
	}

	/**
	 * Update Association
	 *
	 * Met à jour l'association si elle existe
	 * @param  \Illuminate\Http\AssoRequest  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(AssoRequest $request, $id) {
		$asso = Asso::find($id);

		if ($asso) {
			if ($asso->update($request->input()))
				return response()->json($asso, 201);
			else
				return response()->json(['message' => 'L\'association n\'a pas pu être modifiée'], 500);
		}
		else
			return response()->json(['message' => 'L\'association demandée n\'a pas été trouvée'], 404);
	}

	/**
	 * Delete Association
	 *
	 * Supprime l'association si elle existe
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$asso = Asso::find($id);

		if ($asso) {
			if ($asso->destroy())
				return response()->json(['message' => 'L\'assocition a bien été supprimée'], 200);
			else
				return response()->json(['message' => 'L\'association n\'a pas pu être supprimée'], 500);
		}
		else
			return response()->json(['message' => 'L\'association demandée n\'a pas pu être trouvée'], 404);
	}
}
