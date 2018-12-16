<?php
/**
 * Gère en admin les Service.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\{
    Service, Visibility
};

class ServiceController extends ResourceController
{
    protected $model = Service::class;

    /**
     * Définition des champs à afficher.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'name' => 'text',
            'shortname' => 'text',
            'login' => 'text',
            'image' => 'image',
            'description' => 'textarea',
            'url' => 'url',
            'visibility' => Visibility::get(['id', 'name']),
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
            'visibility_id' => Visibility::first()->id,
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
            'visibility'
        ];
    }
}
