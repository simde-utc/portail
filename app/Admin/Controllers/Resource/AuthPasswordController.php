<?php
/**
 * Manages Password Authentifications as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\AuthPassword;
use App\Models\User;

class AuthPasswordController extends ResourceController
{
    protected $model = AuthPassword::class;

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
            'password' => 'text',
            'last_login_at' => 'display',
            'created_at' => 'display',
            'updated_at' => 'display'
        ];
    }

    /**
     * Returns dependencies.
     *
     * @return array
     */
    protected function getWith(): array
    {
        return [
            'user'
        ];
    }

    /**
     * Generates the password's hash before save.
     *
     * @return mixed
     */
    protected function form()
    {
        $form = parent::form();

        $form->saving(function ($form) {
            $form->password = \Hash::make($form->password);
        });

        return $form;
    }
}
