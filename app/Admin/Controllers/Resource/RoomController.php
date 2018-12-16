<?php
/**
 * Gère en admin les Room.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\{
    Room, Location, Visibility, Calendar, User
};

class RoomController extends ResourceController
{
    protected $model = Room::class;

    /**
     * Définition des champs à afficher.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'location' => Location::get(['id', 'name']),
            'visibility' => Visibility::get(['id', 'name']),
            'calendar' => Calendar::get(['id', 'name']),
            'created_by' => 'display',
            'owned_by' => 'display',
            'capacity' => 'number',
            'created_at' => 'display',
            'updated_at' => 'display',
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
            'visibility_id' => Visibility::first()->id,
            'created_by_type' => User::class,
            'created_by_id' => \Auth::guard('admin')->user()->id
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
            'location', 'calendar', 'created_by', 'owned_by', 'visibility',
        ];
    }
}
