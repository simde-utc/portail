<?php
/**
 * Gère en admin les BookingType.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\BookingType;

class BookingTypeController extends ResourceController
{
    protected $model = BookingType::class;

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
            'need_validation' => 'switch',
            'created_at' => 'display',
            'updated_at' => 'display'
        ];
    }
}
