<?php

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

trait HasPlaces
{
	protected function getPlace(Request $request, int $id) {
		if (\Scopes::has($request, 'client-get-locations'))
			$place = Place::with('locations')->find($id);
		else
			$place = Place::find($id);

		if ($place)
			return $place;
		else
			abort(404, 'Impossible de trouver le lieu');
	}
}
