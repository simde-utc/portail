<?php
/**
 * Gestion de la requête pour les calendriers.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class CalendarRequest extends Request
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
                ->length('title')
                ->post('required')
                ->get(),
            'description' => Validation::type('string')
                ->length('text')
                ->get(),
            'color' => Validation::type('string')
                ->length('title')
                ->get(),
            'visibility_id' => Validation::type('uuid')
                ->exists('visibilities', 'id')
                ->post('required')
                ->get(),
            'created_by_type' => Validation::type('string')
                ->get(),
            'created_by_id' => Validation::type('uuid')
                ->get(),
            'owned_by_type' => Validation::type('string')
                ->post('required')
                ->get(),
            'owned_by_id' => Validation::type('uuid')
                ->post('required')
                ->get(),
        ];
    }
}
