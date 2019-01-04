<?php
/**
 * Gestion de la requête pour les détails par utilisateur.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class UserDetailRequest extends Request
{
    /**
     * Défini les règles de validation des champs.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'key' => Validation::type('string')
                ->post('required')
                ->get(),
            'value' => Validation::type('string')
                ->post('required')
                ->patch('required')
                ->get(),
        ];
    }
}
