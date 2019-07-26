<?php
/**
 * Permissions request management.
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

class PermissionRequest extends Request
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
                ->length('name')
                ->unique('permissions', 'type')
                ->post('required')
                ->get(),
            'name' => Validation::type('string')
                ->length('name')
                ->unique('permissions', 'name')
                ->post('required')
                ->get(),
            'description' => Validation::type('string')
                ->length('text')
                ->post('required')
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
