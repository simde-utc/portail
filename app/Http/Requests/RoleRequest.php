<?php
/**
 * Roles request management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class RoleRequest extends Request
{
    /**
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => Validation::type('string')
                ->length('title')
                ->unique('roles', 'name')
                ->post('required')
                ->get(),
            'name' => Validation::type('string')
                ->length('title')
                ->unique('roles', 'name')
                ->post('required')
                ->get(),
            'parent_id' => Validation::type('string')
                ->exists('roles', 'id')
                ->get(),
            'parent_ids' => Validation::type('array')
                ->get(),
            'description' => Validation::type('string')
                ->length('description')
                ->get(),
            'limited_at' => Validation::type('integer')
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
