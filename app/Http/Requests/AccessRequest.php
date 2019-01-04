<?php
/**
 * Gestion de la requête pour les accès.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;
use App\Exceptions\PortailException;
use App\Traits\Model\CanHavePermissions;

class AccessRequest extends Request
{
    /**
     * Défini les règles de validation des champs.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => Validation::type('string')
                ->length('name')
                ->unique('access', 'type')
                ->post('required')
                ->get(),
            'name' => Validation::type('string')
                ->length('name')
                ->unique('access', 'name')
                ->post('required')
                ->get(),
            'description' => Validation::type('string')
                ->length('text')
                ->post('required')
                ->get(),
            'utc_access' => Validation::type('integer')
                ->unique('access', 'utc_access')
                ->get(),
        ];
    }
}
