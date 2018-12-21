<?php
/**
 * Gère en admin les Partner.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\Partner;

class PartnerController extends ResourceController
{
    protected $model = Partner::class;

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
            'description' => 'textarea',
            'image' => 'image',
            'created_at' => 'display',
            'updated_at' => 'display'
        ];
    }
}
