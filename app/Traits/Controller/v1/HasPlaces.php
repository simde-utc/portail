<?php

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

trait HasPlaces
{
	protected function getPlace(Request $request, string $id) {
		$place = Place::find($id);

		if ($place)
			return $place;
		else
			abort(404, 'Impossible de trouver le lieu');
	}
}
