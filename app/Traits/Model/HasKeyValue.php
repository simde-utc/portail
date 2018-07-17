<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\PortailException;

trait HasKeyValue
{
	public function scopeWhereKey($query, $key) {
		if (method_exists($this, $key))
			throw new PortailException('Impossible de récupérer ou de modifier la donnée');

		$query = $query->where('key', strtoupper($key));

		if ($query->count() > 0)
			return $query;
		else
			throw new PortailException('Non trouvé');
	}

	public function scopeKey($query, $key) {
		$model = $this->scopeWhereKey($query, $key)->first();

		if ($model)
			return $model;
		else
			throw new PortailException('Non trouvé');
	}

	public function scopeValueOf($query, $key) {
		$key = strtoupper($key);

		if (method_exists($this, $key))
			return $this->$key($query);

		return $this->scopeKey($query, $key)->value;
	}

	public function scopeToArray($query, $key = null) {
		if ($key)
			return [
				strtolower($key) => $this->scopeValueOf($query, $key)
			];
		else if (count($collection = $query->get()->toArray()) > 0)
			return array_merge(...$collection);
		else
			return [];
	}

	public function scopeAllToArray($query) {
		$data = $this->scopeToArray($query);

		if (property_exists($this, 'functionalKeys')) {
			foreach ($this->functionalKeys as $key) {
				if (method_exists($this, $key)) {
					try {
						$data[$key] = $this->scopeValueOf($query, $key);
					} catch (PortailException $e) {
						$data[$key] = null;
					}
				}
			}
		}

		return $data;
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
					break;

				default:
					$type = 'NULL';
			}

			if ($value instanceof \Carbon\Carbon)
				$type = 'DATETIME';
			elseif (!isset($type))
				$type = 'STRING';

			parent::setAttribute('type', $type);
		}
		else if ($key === 'key') {
			$value = strtoupper($value);

			if (method_exists($this, $value))
				throw new PortailException("Cette information ne peut pas être créée");
		}

		return parent::setAttribute($key, $value);
	}

	public function toArray($all = false) {
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

	public function toJson($all = false) {
		return json_encode($this->toArray($all));
	}

	public function __call($method, $parameters) {
		if (in_array($method, ['increment', 'decrement']))
            return $this->$method(...$parameters);
		else if (method_exists($this->newQuery(), $method))
			return $this->newQuery()->$method(...$parameters);
		else {
			$user_id = isset($parameters[0]) ? ((int) $parameters[0]) : null;
			unset($parameters[0]);

			$model = $this->newQuery()->key($user_id, $method, ...$parameters);

			if ($model)
				return $model->value;
			else
				return null;
		}
	}

	protected function getKeyForSaveQuery()
	{
	    $primaryKeyForSaveQuery = array(count($this->primaryKey));

	    foreach ($this->primaryKey as $i => $pKey) {
	        $primaryKeyForSaveQuery[$i] = isset($this->original[$this->getKeyName()[$i]])
	            ? $this->original[$this->getKeyName()[$i]]
	            : $this->getAttribute($this->getKeyName()[$i]);
	    }

	    return $primaryKeyForSaveQuery;
	}

	/**
	 * Set the keys for a save update query.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	protected function setKeysForSaveQuery(Builder $query)
	{
	    foreach ($this->primaryKey as $i => $pKey) {
	        $query->where($this->getKeyName()[$i], '=', $this->getKeyForSaveQuery()[$i]);
	    }

	    return $query;
	}


	public function user() {
		return $this->belongsTo('App\Models\User');
	}
}
