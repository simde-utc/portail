<?php
/**
 * Gère en admin les authentifications mots de passe.
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
     * Définition des champs à afficher.
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

    /**
     * Génère le hash du mot de passe avant sauvegarde.
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
