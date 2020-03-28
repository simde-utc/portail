<?php
/**
 * Manage UserDetails as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\{
    UserDetail, User
};

class UserDetailController extends ResourceController
{
    protected $model = UserDetail::class;

    protected $name = "Détails utilisateur";

    /**
     * Fields to display definition.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'user' => User::get(['id', 'lastname', 'firstname']),
            'key' => 'text',
            'value' => 'text',
            'type' => 'display',
            'created_at' => 'date',
            'updated_at' => 'date',
        ];
    }

    /**
     * Fields to display labels definition.
     *
     * @return array
     */
    protected function getLabels(): array
    {
        return [
            'user' => 'Utilisateur',
            'key' => 'Clé',
            'value' => 'Valeur',
        ];
    }

    /**
     * Default values definition of the fields to display.
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
