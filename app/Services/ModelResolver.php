<?php
/**
 * ModelResolver Service.
 * Retrieves a class from its name and vice-versa.
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
     * Define the models namespace.
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
     * Return the full model name.
     *
     * @param  string $name
     * @return string
     */
    public function getModelName(string $name)
    {
        return $this->namespace.'\\'.$this->toCamelCase($name);
    }

    /**
     * Find the model class.
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
     * Find a model from its name and id.
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
     * Find the model class from its category.
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
     * Find the model class from its category and id.
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
     * Return the model shortname.
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
     * Return the class shortname.
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
     * Return the model category.
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
     * Return the class shortname.
     *
     * @param  mixed $object
     * @return string
     */
    public function getCategoryFromObject($object)
    {
        return $this->getCategory(get_class($object));
    }

    /**
     * Convert some text in camelcase.
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
     * Convert some text in snakecase.
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
