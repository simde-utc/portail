<?php
/**
 * Gestion de la requête pour les lieux.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;
use App\Exceptions\PortailException;

class LocationRequest extends Request
{
    /**
     * Défini les règles de validation des champs.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => Validation::type('string')
                ->post('required')
                ->get(),
            'latitude' => Validation::type('float')
                ->post('required')
                ->get(),
            'longitude' => Validation::type('float')
                ->post('required')
                ->get(),
            'place_id' => Validation::type('uuid')
                ->exists('places', 'id')
                ->post('required')
                ->get(),
        ];
    }
}
