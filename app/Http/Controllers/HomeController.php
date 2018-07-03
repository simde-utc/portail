<?php

namespace App\Http\Controllers;

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
	public function __construct() {
		$this->middleware('auth');
	}

	/**
	 * Start Page
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function welcome() {
		return view('home');
	}
}
