<?php
/**
 * Gère en admin les types d'association.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\AssoType;

class AssoTypeController extends ResourceController
{
    protected $model = AssoType::class;

    /**
     * Définition des champs à afficher.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'type' => 'display',
            'name' => 'text',
            'created_at' => 'display',
            'updated_at' => 'display'
        ];
    }
}
