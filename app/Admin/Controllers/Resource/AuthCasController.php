<?php
/**
 * Manage CAS authentifications as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\AuthCas;
use App\Models\User;

class AuthCasController extends ResourceController
{
    protected $model = AuthCas::class;

    /**
     * Fields to display definition.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'user' => User::get(['id', 'firstname', 'lastname']),
            'login' => 'text',
            'email' => 'email',
            'is_active' => 'switch',
            'is_confirmed' => 'switch',
            'last_login_at' => 'display',
            'created_at' => 'display',
            'updated_at' => 'display'
        ];
    }

    /**
     * Return dependencies.
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
