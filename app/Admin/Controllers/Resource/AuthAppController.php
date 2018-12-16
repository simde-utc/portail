<?php
/**
 * Gère en admin les authentifications applications.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\AuthApp;
use App\Models\User;

class AuthAppController extends ResourceController
{
    protected $model = AuthApp::class;

    /**
     * Définition des champs à afficher.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'user' => User::get(['id', 'firstname', 'lastname']),
            'app_id' => 'text',
            'password' => 'text',
            'key' => 'text',
            'created_at' => 'display',
            'updated_at' => 'display',
        ];
    }

    /**
     * Retourne les dépendances.
     *
     * @return array
     */
    protected function getWith(): array
    {
        return [
            'user'
        ];
    }
}
