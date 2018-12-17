<?php
/**
 * Gère en admin les ContactType.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\ContactType;

class ContactTypeController extends ResourceController
{
    protected $model = ContactType::class;

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
            'pattern' => 'text',
            'created_at' => 'display',
            'updated_at' => 'display',
        ];
    }
}
