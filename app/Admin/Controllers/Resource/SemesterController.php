<?php
/**
 * Gère en admin les Semester.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\Semester;

class SemesterController extends ResourceController
{
    protected $model = Semester::class;

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
            'year' => 'text',
            'is_spring' => 'switch',
            'begin_at' => 'datetime',
            'end_at' => 'datetime',
            'created_at' => 'display',
            'updated_at' => 'display'
        ];
    }
}
