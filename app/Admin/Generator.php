<?php

namespace App\Admin;

use Illuminate\Support\Arr;
use Encore\Admin\Widgets\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

abstract class Generator
{
    protected $model;
    protected $generated;
    protected $generatedModel;

    public function __construct(string $generated, string $model) {
        $this->model = $model;
        $this->generatedModel = new $model;
        $this->generated = new $generated($this->generatedModel);
    }

    public function get() {
        return $this->generated;
    }

    /**
     * Permet de convertir un tableau pour l'interface admin.
     *
     * @param  array $data
     * @return mixed
     */
    public static function arrayToTable(array $data)
    {
        $rows = [];

        foreach ($data as $key => $value) {
            $value = Generator::adminValue($value);

            if (Arr::isAssoc($data)) {
                $rows[] = ['<b>'.$key.'</b>', $value];
            } else {
                $rows[] = [$value];
            }
        }

        return new Table([], $rows);
    }

    /**
     * Converti les valeurs pour l'admin.
     *
     * @param  mixed $value
     * @return mixed
     */
    public static function adminValue($value, $field = null, $model = null)
    {
        if (is_array($value)) {
            if ($model) {
                $relation = (new $model)->$field();

                if ($relation instanceof Relation) {
                    $must = $relation->getModel()->getMustFields();

                    foreach (array_keys($value) as $key) {
                        if (!in_array($key, $must)) {
                            unset($value[$key]);
                        }
                    }
                }
            }

            return Generator::arrayToTable($value);
        } else if (is_bool($value)) {
            return $value ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>';
        } else if (is_null($value)) {
            return '<i class="fa fa-question text-warning"></i>';
        }

        try {
            $date = new \Carbon\Carbon($value);

            return $date->format('d/m/Y à H:m');
        } catch (\Exception $e) {}

        try {
            if (is_array($array = json_decode($value, true))) {
                return Generator::arrayToTable($array);
            }
        } catch (\Exception $e) {}

        return $value;
    }

    abstract protected function callCustomMethods($field);

    public function addFields(array $fields) {
        foreach ($fields as $field) {
            $this->generateField($field);
        }

        return $this;
    }

    protected function generateField($field) {
        $model = $this->model;

        $this->callCustomMethods($this->generated->$field())
            ->{$this->valueMethod}(function ($value) use ($field, $model) {
                return Generator::adminValue($value, $field, $model);
            });
    }

    public function __call($method, $args)
    {
        return call_user_func([$this->generated, $method], ...$args);
    }
}
