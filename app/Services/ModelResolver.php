<?php
/**
 * Service ModelResolver.
 * Permet de récupérer une classe à partir de son nom et inversement
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Services;

use Illuminate\Http\Request;
use Lcobucci\JWT\Parser;
use Laravel\Passport\Token;
use App\Models\Client;
use App\Exceptions\PortailException;

class ModelResolver
{
    protected $namespace = 'App\Models';

    /**
     * Défini le namespace des modèles.
     *
     * @param string $namespace
     * @return ModelResolver
     */
    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Donne le nom entier du modèle.
     *
     * @param  string $name
     * @return string
     */
    public function getModelName(string $name)
    {
        return $this->namespace.'\\'.$this->toCamelCase($name);
    }

    /**
     * Retrouve la classe du modèle.
     *
     * @param  string $name
     * @param  mixed  $instance
     * @return mixed
     */
    public function getModel(string $name, $instance=null)
    {
        $model = resolve($this->getModelName($name));

        if ($instance === null || ($model instanceof $instance)) {
            return $model;
        } else {
            throw new PortailException('Le type donné n\'est pas valable');
        }
    }

    /**
     * Retrouve un modèle à partir de son nom et de son id.
     *
     * @param  string $name
     * @param  string $model_id
     * @param  mixed  $instance
     * @return mixed
     */
    public function findModel(string $name, string $model_id, $instance=null)
    {
        return $this->getModel($name, $instance)->find($model_id);
    }

    /**
     * Retrouve la classe du modèle à partir de sa catégorie.
     *
     * @param  string $name
     * @param  mixed  $instance
     * @return mixed
     */
    public function getModelFromCategory(string $name, $instance=null)
    {
        if (substr($name, -3) === 'ies') {
            $singular = substr($name, 0, -1).'y';
        } else {
            $singular = substr($name, 0, -1);
        }

        return $this->getModel($singular, $instance);
    }

    /**
     * Retrouve un modèle à partir de son catégorie et de son id.
     *
     * @param  string $name
     * @param  string $model_id
     * @param  mixed  $instance
     * @return mixed
     */
    public function findModelFromCategory(string $name, string $model_id, $instance=null)
    {
        return $this->getModelFromCategory($name, $instance)->find($model_id);
    }

    /**
     * Donne le nom court du modèle.
     *
     * @param  string $modelName
     * @param  string $delimiter
     * @return string
     */
    public function getName(string $modelName, string $delimiter='_')
    {
        return $this->toSnakeCase((new \ReflectionClass($modelName))->getShortName(), $delimiter);
    }

    /**
     * Donne le nom court de la classe.
     *
     * @param  mixed  $object
     * @param  string $delimiter
     * @return string
     */
    public function getNameFromObject($object, string $delimiter='_')
    {
        return $this->getName(get_class($object), $delimiter);
    }

    /**
     * Donne la catégorie du modèle.
     *
     * @param  string $modelName
     * @param  string $delimiter
     * @return string
     */
    public function getCategory(string $modelName, string $delimiter='_')
    {
        $name = $this->getName($modelName, $delimiter);

        if (substr($name, 0, -1) === 'y') {
            return substr($name, 0, -1).'ies';
        } else if (substr($name, 0, -1) === 's') {
            return $name;
        } else {
            return $name.'s';
        }
    }

    /**
     * Donne le nom court de la classe.
     *
     * @param  mixed $object
     * @return string
     */
    public function getCategoryFromObject($object)
    {
        return $this->getCategory(get_class($object));
    }

    /**
     * Converti du texte en camelcase.
     *
     * @param  string $name
     * @param  string $delimiter
     * @return string
     */
    public function toCamelCase(string $name, string $delimiter='')
    {
        return str_replace('_', $delimiter, ucwords($name, '_'));
    }

    /**
     * Converti du texte en snakecase.
     *
     * @param  string $name
     * @param  string $delimiter
     * @return string
     */
    public function toSnakeCase(string $name, string $delimiter='_')
    {
        $name[0] = strtolower($name[0]);

        return strtolower(preg_replace('/([A-Z])/', $delimiter.'\\1', $name));
    }
}
