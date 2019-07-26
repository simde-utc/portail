<?php
/**
 * Roles by user request manageent.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class UserRoleRequest extends Request
{
    /**
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'role_id' => Validation::type('uuid')
                ->exists('roles', 'id')
                ->post('required')
                ->get(),
            'validated_by_id' => Validation::type('uuid')
                ->exists('users', 'id')
                ->get(),
            'semester_id' => Validation::type('uuid')
                ->exists('semesters', 'id')
                ->post('required')
                ->get(),
        ];
    }
}
