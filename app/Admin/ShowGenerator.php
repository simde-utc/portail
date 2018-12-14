<?php
/**
 * Génère une présentation admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin;

use Encore\Admin\Show;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\Relation;

class ShowGenerator extends Generator
{
    /**
     * Méthode correspondante à l'admin (qui change en fonction du type de formulaire...).
     *
     * @var string
     */
    protected $valueMethod = 'as';

    /**
     * Crée la présentation avec notre model
     *
     * @param mixed $model Modèle de la ressource à manipuler.
     */
    public function __construct($model)
    {
        $this->model = get_class($model);
        $this->generatedModel = $model;
        $this->generated = new Show($model);
    }

    /**
     * Indique qu'on peut afficher en html nos infos.
     *
     * @param  mixed $field
     * @return mixed
     */
    protected function callCustomMethods($field)
    {
        return $field->unescape();
    }

    /**
     * Génère un nouveau champ et les champs liés.
     *
     * @param  string $field
     * @return void
     */
    protected function generateField(string $field)
    {
        if (method_exists($this->generatedModel, $field)
            && (($relation = $this->generatedModel->$field()) instanceof Relation)) {
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
        } else {
            parent::generateField($field);
        }
    }
}
