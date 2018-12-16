<?php
/**
 * Gère en admin les Reservation.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\{
    Reservation, Room, ReservationType, Event, User
};

class ReservationController extends ResourceController
{
    protected $model = Reservation::class;

    /**
     * Définition des champs à afficher.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'room' => Room::with('location')->get(['id', 'location_id']),
            'type' => ReservationType::get(['id', 'name']),
            'event' => Event::get(['id', 'name']),
            'description' => 'textarea',
            'created_by' => 'display',
            'owned_by' => 'display',
            'validated_by' => 'display',
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
            'room', 'type', 'created_by', 'owned_by', 'validated_by'
        ];
    }
}
