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

    /**
     * Génère un filtre admin.
     *
     * @param  mixed  $filter
     * @param  string $field
     * @param  mixed  $data
     * @param  mixed  $model
     * @return mixed
     */
    public static function generateFilter($filter, string $field, $data, $model)
    {
        if (is_string($data)) {
            if ($data === 'datetime' || in_array($field, ['deleted_at', 'created_at', 'updated_at'])) {
                $filter->lt($field)->datetime();
                $filter->equal($field)->datetime();
                $filter->gt($field)->datetime();
            } else {
                switch ($data) {
                    case 'switch':
                        $filter->checkbox($field);
                        break;

                    case 'display':
                    case 'text':
                    case 'textarea':
                    default:
                        $filter->like($field);
                }
            }
        } else {
            $options = [];
            $name = $field;

            foreach ($data as $value) {
                $options[$value->id] = $value->name;
            }

            if (method_exists($model, $field)
                && (($relation = $model->$field()) instanceof Relation)
                && $data instanceof Collection) {
                $name = ucfirst($field);
                $field .= '_id';
            }

            $filter->equal($field, $name)->multipleSelect($options);
        }
    }

    /**
     * Permet d'ajouter plusieurs champs.
     *
     * @param array $fields
     * @return Generator
     */
    public function addFields(array $fields)
    {
        parent::addFields(array_keys($fields));

        $model = $this->generatedModel;

        $this->generated->filter(function ($filter) use ($fields, $model) {
            $filter->disableIdFilter();

            foreach ($fields as $field => $data) {
                GridGenerator::generateFilter($filter, $field, $data, $model);
            }
        });

        return $this;
    }
}
