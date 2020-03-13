<?php
/**
 * Generate a global admin presentation.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Corentin Mercier <corentin@cmercier.fr>
 * @author Noé Amiot <noe.amiot@etu.utc.fr>
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
     * Method corresponding to the admin (which changes according to the type of form...).
     *
     * @var string
     */
    protected $valueMethod = 'display';

    /**
     * Prepare main display.
     *
     * @param string $model
     */
    public function __construct(string $model)
    {
        parent::__construct(Grid::class, $model);
    }

    /**
     * Indicate that the field can be put in order.
     *
     * @param  mixed $field
     * @return mixed
     */
    protected function callCustomMethods($field)
    {
        return $field->sortable();
    }

    /**
     * Generate an admin filter.
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
            if ($data === 'date' || $data === 'datetime' || in_array($field, ['deleted_at', 'created_at', 'updated_at'])) {
                $filter->between($field)->date();
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
     * Allow to add several fields
     *
     * @param array   $fields
     * @param boolean $generateFilters
     * @return Generator
     */
    public function addFields(array $fields, bool $generateFilters=true)
    {
        parent::addFields(array_keys($fields));

        $model = $this->generatedModel;

        if ($generateFilters) {
            $this->generated->filter(function ($filter) use ($fields, $model) {
                $filter->disableIdFilter();

                foreach ($fields as $field => $data) {
                    GridGenerator::generateFilter($filter, $field, $data, $model);
                }
            });
        }

        return $this;
    }
}
