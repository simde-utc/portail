<?php

namespace App\Http\Controllers;

use App\Models\Asso;
use App\Http\Requests\AssoRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssoController extends Controller
{
	public function __construct() {
		// $this->middleware('auth:api', ['except' => ['index', 'show']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$assos = Asso::get();
		return response()->json($assos, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(AssoRequest $request) {
		$asso = Asso::create($request->input());
		if ($asso)
			return response()->json($asso, 200);
		else
			return response()->json(["message" => "Impossible de créer l'association"], 500);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
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
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}
