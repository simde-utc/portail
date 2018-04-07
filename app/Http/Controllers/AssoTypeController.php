<?php

namespace App\Http\Controllers;

use App\Models\AssoType;
use App\Http\Requests\AssoTypeRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssoTypeController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$types = AssoType::get();
		return response()->json($types, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(AssoTypeRequest $request) {
		$type = AssoType::create($request->input());
		if ($type)
			return response()->json($type, 200);
		else
			return response()->json(["message" => "Impossible de créer le type"], 500);
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
