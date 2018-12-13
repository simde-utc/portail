<?php

namespace App\Admin;

use Encore\Admin\Form;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class FormGenerator extends Generator
{
    public $valueMethod = 'with';

    public function __construct(string $model) {
        parent::__construct(Form::class, $model);
    }

    protected function callCustomMethods($field) {
        return $field;
    }

    public function addFields(array $fields, array $defaults = null) {
        $model = $this->model;

        foreach ($fields as $field => $type) {
            if (method_exists($this->generatedModel, $field)
                && (($relation = $this->generatedModel->$field()) instanceof Relation)
                && $type instanceof Collection) {
                $options = [];

                $name = ucfirst($field);
                $field = $relation->getForeignKey();
                $generatedField = $this->generated->select($field, $name);

                foreach ($type as $instance) {
                    $options[$instance->id] = $instance->name;
                }

                $generatedField->options($options);
            } else {
                if (is_array($type)) {
                    $generatedField = $this->generated->select($field);
                    $generatedField->options($type);
                } else {
                    $generatedField = $this->generated->$type($field);

                    if ($type === 'display') {
                        $this->callCustomMethods($generatedField)->{$this->valueMethod}(function ($value) use ($field, $model) {
                            return Generator::adminValue($value, $field, $model);
                        });
                    }
                }
            }

            if (isset($defaults[$field])) {
                $generatedField->default($defaults[$field]);
            }
        }

        return $this;
    }
}
