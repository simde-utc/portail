<?php
/**
 * Add the controller an access to aux emplacements.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasPosition;

trait HasPlaces
{
    use HasPosition;

    /**
     * Retrieve un emplacement.
     *
     * @param  Request $request
     * @param  string  $place_id
     * @return Place|null
     */
    protected function getPlace(Request $request, string $place_id)
    {
        $place = Place::findSelection($place_id);

        if ($place) {
            return $place;
        } else {
            abort(404, 'Impossible de trouver le lieu');
        }
    }
}
