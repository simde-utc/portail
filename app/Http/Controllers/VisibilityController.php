<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisibilityRequest;
use Illuminate\Http\Request;
use App\Models\Visibility;


/**
 * @resource Visibility
 *
 * Gestion des visibilités
 */
class VisibilityController extends Controller
{
	/**
	 * List Visibilities
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */

	 public function index()
	{
		$visibilities = Visibility::get();

		return response()->json($visibilities, 200);
	}

	/**
	 * Create Visibility
	 *
	 * @param \Illuminate\Http\VisibilityRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(VisibilityRequest $request)
	{
		$visibility = Visibility::create($request->all());

		if ($visibility)
			return response()->json($visibility, 200);
		else
			return response()->json(['message' => 'Impossible de créer la visibilité'], 500);

	}

	/**
	 * Show Visibility
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$visibility = Visibility::find($id);

		if ($visibility)
			return response()->json($visibility, 200);
		else
			return response()->json(['message' => 'Impossible de trouver la visibilité'], 500);
	}

	/**
	 * Update Visibility
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(VisibilityRequest $request, $id)
	{
		$visibility = Visibility::find($id);

		if ($visibility) {
			if ($visibility->update($request->input()))
				return response()->json($visibility, 201);
			else
				return response()->json(['message' => 'Impossible de mettre à jour la visibilité'],500);
		}
		else
			return response()->json(['message' => 'Impossible de trouver la  visibilité'], 500);
	}

	/**
	 * Delete Visibility
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
	   $visibility = Visibility::find($id);

		if ($visibility) {
			if ($visibility->delete())
				return response()->json(['message' => 'La visibilité a bien été supprimée'], 200);
			else
				return response()->json(['message' => 'Erreur lors de la suppression de la visibilité'], 500);
		}
		else
			return response()->json(['message' => 'Impossible de trouver la visibilité'], 500);
	}
}
