<?php
/**
 * Gère en admin les accès.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\Access;

class AccessController extends ResourceController
{
    protected $model = Access::class;

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
            'description' => 'text',
            'utc_access' => 'number',
            'created_at' => 'display',
            'updated_at' => 'display'
        ];
    }
}
