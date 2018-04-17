<?php

namespace App\Http\Controllers;

use App\Models\AssoType;
use App\Http\Requests\AssoTypeRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @resource AssoType
 *
 * Gère les types d'associations
 */
class AssoTypeController extends Controller
{
	/**
	 * List AssoTypes
	 *
	 * Retourne la liste des types d'associations
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$types = AssoType::get();
		return response()->json($types, 200);
	}

	/**
	 * Create AssoType
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(AssoTypeRequest $request) {
		$type = AssoType::create($request->input());
		if ($type)
			return response()->json($type, 200);
		else
			return response()->json(['message' => 'Impossible de créer le type'], 500);
	}

	/**
	 * Show AssoType
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Update AssoType
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Delete AssoType
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}
