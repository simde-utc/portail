<?php
/**
 * Association members request management.
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

class AssoMemberRequest extends Request
{
    /**
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => Validation::type('uuid')
                ->exists('users', 'id')
                ->get(),
            'role_id' => Validation::type('uuid')
                ->exists('roles', 'id')
                ->post('required')
                ->get(),
            'semester_id' => Validation::type('uuid')
                ->exists('semesters', 'id')
                ->get(),
        ];
    }
}
