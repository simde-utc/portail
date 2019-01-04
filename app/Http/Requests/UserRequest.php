<?php
/**
 * Gestion de la requête pour les services des utilisateurs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class UserRequest extends Request
{
    /**
     * Défini les règles de validation des champs.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => Validation::type('string')
                ->post('required')
                ->get(),
            'lastname' => Validation::type('string')
                ->post('required')
                ->get(),
            'firstname' => Validation::type('string')
                ->post('required')
                ->get(),
            'is_active' => Validation::type('boolean')
                ->get(),
            'details' => Validation::type('array')
                ->get(),
            'preferences' => Validation::type('array')
                ->get(),
        ];
    }
}
