<?php
/**
 * User services request management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class UserServiceRequest extends Request
{
    /**
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service_ids' => Validation::type('array')
                ->get(),
            'service_id' => Validation::type('uuid')
                ->exists('services', 'id')
                ->get(),
        ];
    }
}
