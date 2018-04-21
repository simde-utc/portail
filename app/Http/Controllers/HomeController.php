<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;

/**
 * @resource Home
 *
 * Affichage des pages d'accueil et de gestion User
 */
class HomeController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth', ['except' => 'welcome']);
	}

	/**
	 * Start Page
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function welcome() {
		$groups = Group::withVisible();
		return response()->json($groups, 200);
		return view('welcome');
	}

	/**
	 * User Dashboard
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		return view('home');
	}
}
