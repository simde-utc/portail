<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Permet de savoir quoi afficher
	 * @param  Request $request
	 * @param  array   $choices
	 * @return array
	 */
	protected function getChoices(Request $request, array $choices) {
		$only = $request->input('only') ? explode(',', $request->input('only')) : [];
		$except = $request->input('except') ? explode(',', $request->input('except')) : [];

		if (count($only) > 0)
			$choices = $only;

		foreach ($except as $choice) {
			if (($key = array_search($choice, $choices)) !== false)
			    unset($choices[$key]);
		}

		return $choices;
	}
}
