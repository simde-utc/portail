<?php
/**
 * Génère une présentation globale admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin;

use Encore\Admin\Grid;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class GridGenerator extends Generator
{
    /**
     * Méthode correspondante à l'admin (qui change en fonction du type de formulaire...).
     *
     * @var string
     */
    protected $valueMethod = 'display';

    /**
     * Prépare l'affichage principal.
     *
     * @param string $model
     */
    public function __construct(string $model)
    {
        parent::__construct(Grid::class, $model);
    }

    /**
     * Indique que le champ peut être mis dans l'ordre.
     *
     * @param  mixed $field
     * @return mixed
     */
    protected function callCustomMethods($field)
    {
        return $field->sortable();
    }
}
