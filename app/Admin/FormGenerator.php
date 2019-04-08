<?php
/**
 * Génère un formulaire admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin;

use Encore\Admin\Form;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Facades\ModelResolver;

class FormGenerator extends Generator
{
    /**
     * Méthode correspondante à l'admin (qui change en fonction du type de formulaire...).
     *
     * @var string
     */
    protected $valueMethod = 'with';

    /**
     * Prépare le formulaire.
     *
     * @param string $model
     */
    public function __construct(string $model)
    {
        parent::__construct(Form::class, $model);
    }

    /**
     * Indique rien de spécial.
     *
     * @param  mixed $field
     * @return mixed
     */
    protected function callCustomMethods($field)
    {
        return $field;
    }

    /**
     * Permet d'ajouter plusieurs champs.
     *
     * @param array $fields
     * @param array $defaults
     * @return FormGenerator
     */
    public function addFields(array $fields, array $defaults=null)
    {
        $model = $this->model;

        foreach ($fields as $field => $type) {
            if (method_exists($this->generatedModel, $field)
                && (($relation = $this->generatedModel->$field()) instanceof Relation)
                && $type instanceof Collection) {
                $options = [];

                $name = ucfirst($field);
                $field .= '_id';
                $generatedField = $this->generated->select($field, $name);

                foreach ($type as $instance) {
                    $options[FormGenerator::adminValue($instance->id)] = FormGenerator::adminValue($instance->name);
                }

                $generatedField->options($options);
            } else {
                if (is_array($type)) {
                    $generatedField = $this->generated->select($field);
                    $generatedField->options($type);
                } else {
                    $generatedField = $this->generated->$type($field);
                    if ($type === 'image') {
                        $this->get()->submitted(function (Form $form) {
                            $form->model()->id = ($form->model()->id ?? \Uuid::generate()->string);
                        });

                        $generatedField->name(function ($file) {
                            $path = ModelResolver::getCategory(get_class($this->form->model())).'/'.$this->form->model()->id;
                            $name = time().'.'.$file->getClientOriginalExtension();

                            return $path.'/'.$name;
                        });

                        $this->get()->saved(function (Form $form) use ($field) {
                            $model = $form->model();

                            $model->$field = url($model->$field);
                            $model->save();
                        });
                    } else if ($type === 'display') {
                        $this->callCustomMethods($generatedField)->{$this->valueMethod}(function ($value) use ($field, $model) {
                            return FormGenerator::adminValue($value, $field, $model);
                        });
                    }
                }
            }

            if (isset($defaults[$field])) {
                $generatedField->default(e($defaults[$field]));
            }
        }

        return $this;
    }
}
