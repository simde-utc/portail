<?php
/**
 * Gère en admin les clients.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\Asso;

class ClientController extends ResourceController
{
    protected $model = Client::class;

    /**
     * Définition des champs à afficher.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'user' => User::get(['id', 'lastname', 'firstname']),
            'asso' => Asso::get(['id', 'name']),
            'name' => 'text',
            'secret' => 'text',
            'redirect' => 'text',
            'targeted_types' => 'text',
            'personal_access_client' => 'switch',
            'password_client' => 'switch',
            'created_at' => 'display',
            'updated_at' => 'display'
        ];
    }

    /**
     * Définition des valeurs par défaut champs à afficher.
     *
     * @return array
     */
    protected function getDefaults(): array
    {
        return [
            'user_id' => \Auth::guard('admin')->id(),
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
            'user', 'asso'
        ];
    }
}
