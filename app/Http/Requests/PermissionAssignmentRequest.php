<?php
/**
 * Gestion de la requête pour les permissions.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;
use App\Exceptions\PortailException;

class PermissionAssignmentRequest extends Request
{
    /**
     * Défini les règles de validation des champs.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => Validation::type('uuid')
                ->exists('users', 'id')
                ->get(),
            'validated_by' => Validation::type('uuid')
                ->exists('users', 'id')
                ->get(),
            'permission_id' => Validation::type('uuid')
                ->exists('permissions', 'id')
                ->get(),
            'semester_id' => Validation::type('uuid')
                ->exists('semesters', 'id')
                ->get(),
        ];
    }
}
