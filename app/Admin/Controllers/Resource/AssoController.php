<?php
/**
 * Gère en admin les associations.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\Asso;
use App\Models\AssoType;

class AssoController extends ResourceController
{
    protected $model = Asso::class;

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
            'type' => AssoType::get(['id', 'name']),
            'parent' => Asso::get(['id', 'name']),
            'created_at' => 'display',
            'updated_at' => 'display',
            'deleted_at' => 'date',
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
            'type', 'parent'
        ];
    }
}
