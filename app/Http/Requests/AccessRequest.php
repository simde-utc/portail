<?php
/**
 * Access request management.
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
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'access_id' => Validation::type('uuid')
        ->exists('access', 'id')
                ->post('required')
        ->get(),
            'user_id' => Validation::type('uuid')
        ->exists('users', 'id')
        ->get(),
            'description' => Validation::type('string')
                ->length('description')
                ->post('required')
                ->get(),
            'validate' => Validation::type('boolean')
                ->get(),
            'comment' => Validation::type('string')
                ->length('description')
                ->get(),
        ];
    }
}
