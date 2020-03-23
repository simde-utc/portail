<?php
/**
 * Manage visibilities as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\Visibility;

class VisibilityController extends ResourceController
{
    protected $model = Visibility::class;

    /**
     * Field to display definition.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'type' => 'display',
            'name' => 'text',
            'parent' => Visibility::get(['id', 'name']),
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
            'parent_id' => Visibility::orderBy('created_at', 'DESC')->first()->id,
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
            'parent'
        ];
    }
}
