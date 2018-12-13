<?php

namespace App\Admin;

use Encore\Admin\Show;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\Relation;

class ShowGenerator extends Generator
{
    public $valueMethod = 'as';

    public function __construct($model) {
        $this->model = get_class($model);
        $this->generatedModel = $model;
        $this->generated = new Show($model);
    }

    protected function callCustomMethods($field) {
        return $field->unescape();
    }

    protected function generateField($field) {
        if (method_exists($this->generatedModel, $field) && (($relation = $this->generatedModel->$field()) instanceof Relation)) {
            $must = $relation->getModel()->getMustFields();
            $resource = Str::plural($relation->getModel());
            $this->generated->$field($field, function ($value) use ($must, $resource) {
                $value->setResource('/admin/'.$resource);

                foreach ($must as $key) {
                    $this->callCustomMethods($value->$key())
                        ->{$this->valueMethod}(function ($value) {
                            return ShowGenerator::adminValue($value);
                        });
                }
            });
        }
        else {
            parent::generateField($field);
        }
    }
}
