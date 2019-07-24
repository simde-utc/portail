<?php
/**
 * Notification by user request management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class UserNotificationRequest extends Request
{
    /**
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'notifier' => Validation::type('string')
                ->get(),
            'content' => Validation::type('string')
                ->post('required')
                ->get(),
            'action' => Validation::type('array')
                ->post('required')
                ->get(),
        ];
    }
}
