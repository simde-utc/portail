<?php
/**
 * Gestion de la requête pour les notifications par utilisateur.
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
     * Défini les règles de validation des champs.
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
