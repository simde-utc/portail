<?php
/**
 * User favorite associations request management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class UserAssoRequest extends Request
{
    /**
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'asso_id' => Validation::type('uuid')
                ->exists('assos', 'id')
                ->post('required')
                ->get(),
        ];
    }
}
