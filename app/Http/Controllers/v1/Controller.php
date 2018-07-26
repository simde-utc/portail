<?php

namespace App\Http\Controllers\v1;

use App\Models\User;
use App\Models\Asso;
use App\Models\Group;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use App\Exceptions\PortailException;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
	/**
	 * Permet de savoir quoi afficher
	 * @param  Request $request
	 * @param  array $choices
	 * @return array
	 * @throws PortailException
	 */
	protected function getChoices(Request $request, array $choices) {
		$only = $request->input('only') ? explode(',', $request->input('only')) : [];
		$except = $request->input('except') ? explode(',', $request->input('except')) : [];

		if (count(array_intersect($only, $choices)) !== count($only) || count(array_intersect($except, $choices)) !== count($except))
			throw new PortailException('Il n\'est possible de spécifier pour only et except que: '.implode(', ', $choices));

		if (count($only) > 0)
			$choices = $only;

		foreach ($except as $choice) {
			if (($key = array_search($choice, $choices)) !== false)
				unset($choices[$key]);
		}

		return $choices;
	}
}
