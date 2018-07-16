<?php

namespace App\Services;

use Illuminate\Http\Request;
use Lcobucci\JWT\Parser;
use Laravel\Passport\Token;
use App\Models\Client;
use App\Exceptions\PortailException;

/**
 * Cette classe permet de récupérer une classe à partir de son nom et inversement
 */
class ModelResolver {
	protected $namespace = '\App\Models';

	public function setNamespace($namespace) {
		$this->namespace = $namespace;

		return $this;
	}

	public function getModelName($name) {
		return $this->namespace.'\\'.ucfirst($name);
	}

	public function getModel($name, $instance = null) {
		$model = resolve($this->getModelName($name));

		if ($instance === null || ($model instanceof $instance))
			return $model;
		else
			throw new PortailException('Le type donné n\'est pas valable');
	}

	public function getModelFromCategory($name, $instance = null) {
		if (substr($name, -3) === 'ies')
			$singular = substr($name, 0, -1).'y';
		else
			$singular = substr($name, 0, -1);

		return $this->getModel($singular, $instance);
	}

	public function getName($modelName) {
		return strtolower((new \ReflectionClass($modelName))->getShortName());
	}

	public function getCategory($modelName) {
		$name = $this->getName($modelName);

		if (substr($name, 0, -1) === 'y')
			return substr($name, 0, -1).'ies';
		else if (substr($name, 0, -1) === 's')
			return $name;
		else
			return $name.'s';
	}
}
