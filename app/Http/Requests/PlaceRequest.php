<?php
/**
 * Gestion de la requête pour les emplaements.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;
use App\Exceptions\PortailException;

class PlaceRequest extends Request
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
            'address' => Validation::type('string')
                ->post('required')
                ->get(),
            'city' => Validation::type('string')
                ->post('required')
                ->get(),
            'country' => Validation::type('string')
                ->post('required')
                ->get(),
            'latitude' => Validation::type('float')
                ->post('required')
                ->get(),
            'longitude' => Validation::type('float')
                ->post('required')
                ->get(),
        ];
    }
}
