<?php
/**
 * Classe abstraite de génération d'un type de formulaire.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin;

use Illuminate\Support\{
    Arr, HtmlString
};
use Encore\Admin\Widgets\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

abstract class Generator
{
    protected $model;
    protected $generated;
    protected $generatedModel;
    protected $valueMethod;

    protected const CHECK_ICON = '<i class="fa fa-check text-success"></i>' ;
    protected const TIMES_ICON = '<i class="fa fa-times text-danger"></i>' ;
    protected const QUESTION_ICON = '<i class="fa fa-question text-warning"></i>' ;

    /**
     * Prépare la génréation.
     *
     * @param string $generated
     * @param string $model
     */
    public function __construct(string $generated, string $model)
    {
        $this->model = $model;
        $this->generatedModel = new $model;
        $this->generated = new $generated($this->generatedModel);
    }

    /**
     * Retourne le formulaire généré.
     *
     * @return mixed
     */
    public function get()
    {
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
                $rows[] = ['<b>'.e($key).'</b>', $value];
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
     * @param  mixed $field
     * @param  mixed $model
     * @return mixed
     */
    public static function adminValue($value, $field=null, $model=null)
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
            return new HtmlString($value ? static::CHECK_ICON : static::TIMES_ICON);
        } else if (is_null($value)) {
            return new HtmlString(static::QUESTION_ICON);
        }

        try {
            $date = new \Carbon\Carbon($value);

            return $date->format('d/m/Y à H:m');
        } catch (\Exception $e) {
            // Ce champ n'est pas une date.
        }

        try {
            if (is_array($array = json_decode($value, true))) {
                return Generator::arrayToTable($array);
            }
        } catch (\Exception $e) {
            // Ce champ n'est pas un json.
        }

        if (is_string($value)) {
            // L'appel e($value) permet de rendre la valeur saine #SansInjections.
            return e($value);
        } else {
            return $value;
        }
    }

    /**
     * Définition sépécifique de la part du type de formulaire.
     *
     * @param mixed $field
     * @return mixed
     */
    abstract protected function callCustomMethods($field);

    /**
     * Permet d'ajouter plusieurs champs.
     *
     * @param array $fields
     * @return Generator
     */
    public function addFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->generateField($field);
        }

        return $this;
    }

    /**
     * Génère un nouveau champ.
     *
     * @param  string $field
     * @return void
     */
    protected function generateField(string $field)
    {
        $model = $this->model;

        $this->callCustomMethods($this->generated->$field())
            ->{$this->valueMethod}(function ($value) use ($field, $model) {
                return Generator::adminValue($value, $field, $model);
            });
    }

    /**
     * Renvoie tous les appels sur le formulaire.
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return call_user_func([$this->generated, $method], ...$args);
    }
}
