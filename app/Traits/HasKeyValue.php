<?php

namespace App\Traits;

trait HasKeyValue
{
	public static function find(int $id = null, string $key = '*') {
		return (new static)->keys($id, $key);
	}

	public function keys(int $id = null, string $key = '*') {
		$collection = $id ? $this->where($this->primaryKey, $id) : $this;

		if ($key === '*')
			return $collection->get();
		else
			return $collection->where('key', strtoupper($key))->first();
	}

	public static function findToArray(int $id = null, string $key = '*') {
		return (new static)->keysToArray($id, $key);
	}

	public function keysToArray(int $id = null, string $key = '*') {
		$collection = $id ? $this->where($this->primaryKey, $id) : $this;
		$array = [];

		if ($key === '*')
			$collection = $collection->get();
		else
			$collection = collect($collection->where('key', strtoupper($key))->first());

		foreach ($collection as $model)
			$array[strtolower($model->key)] = $model->value;

		return $array;
	}

	public static function allToArray(int $id) {
		$thus = (new static);
		$array = $thus->keysToArray($id);

		if (property_exists($thus, 'valuesInFunction')) {
			foreach ($thus->valuesInFunction as $value)
				$array[$value] = $thus->$value($id);
		}

		return $array;
	}

	public function getAttribute($key) {
		$value = parent::getAttribute($key);

		if ($key === 'value') {
			switch ($this->type) {
				case 'STRING':
					return $value;

				case 'INTEGER':
					return (int) $value;

				case 'DOUBLE':
					return (double) $value;

				case 'BOOLEAN':
					return (boolean) $value;

				case 'ARRAY':
					return json_decode($value, true);

				case 'DATETIME':
					return \Carbon\Carbon::parse($value);

				default:
					return null;
			}
		}

		return $value;
	}

	public function setAttribute($key, $value) {
		if ($key === 'value') {
			switch (gettype($value)) {
				case 'string':
					if (\DateTime::createFromFormat('Y-m-d H:i:s', $value) || \DateTime::createFromFormat('Y-m-d', $value) || \DateTime::createFromFormat('H:i:s', $value))
						$value = \Carbon\Carbon::parse($value);
					else
						$type = 'STRING';
					break;

				case 'integer':
					$type = $type ?? 'INTEGER';
				case 'double':
					$type = $type ?? 'DOUBLE';
				case 'boolean':
					$type = $type ?? 'BOOLEAN';
					$value = (string) $value;
					break;

				case 'array':
					$type = $type ?? 'ARRAY';
					$value = json_encode($value);

				default:
					$type = 'NULL';
			}

			if ($value instanceof \Carbon\Carbon)
				$type = 'DATETIME';
			elseif (!isset($type))
				$type = 'STRING';

			parent::setAttribute('type', $type);
		}
		else if ($key === 'key')
			$value = strtoupper($value);

		return parent::setAttribute($key, $value);
	}

	public function toArray($all = true) {
		if ($all) {
			$array = parent::toArray();
			$array['value'] = $this->getAttribute('value');

			return $array;
		}
		else {
			return [
				strtolower($this->key) => $this->value,
			];
		}
	}

	public function toJson($all = true) {
		return json_encode(parent::toArray($all));
	}

	public function __call($method, $parameters) {
		if (in_array($method, ['increment', 'decrement']))
            return $this->$method(...$parameters);
		else if (method_exists($this->newQuery(), $method))
			return $this->newQuery()->$method(...$parameters);
		else {
			$user_id = isset($parameters[0]) ? ((int) $parameters[0]) : null;
			unset($parameters[0]);

			$model = $this->keys($user_id, $method, ...$parameters);

			if ($model)
				return $model->value;
			else
				return null;
		}
	}

	public function user() {
		return $this->belongsTo('App\Models\User');
	}
}
