<?php
/**
 * Manage Contacts as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\{
    User, Contact, ContactType, Visibility
};

class ContactController extends ResourceController
{
    protected $model = Contact::class;

    /**
     * Fields to display definition.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'name' => 'text',
            'value' => 'text',
            'type' => ContactType::get(['id', 'name']),
            'visibility' => Visibility::get(['id', 'name']),
            'owned_by' => 'display',
            'created_at' => 'date',
            'updated_at' => 'date',
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
            'visibility_id' => Visibility::first()->id,
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
            'type', 'visibility', 'owned_by'
        ];
    }
}
