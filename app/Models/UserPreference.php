<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
	protected $table = 'users_preferences';
	protected $primaryKey = 'user_id';
	protected $fillable = [
		'user_id', 'key', 'value', 'type',
	];

	public static function find(int $user_id, string $key = '*') {
		$prefs = static::where('user_id', $user_id);

		if ($key === '*')
			return $prefs->get();
		else
			return $prefs->where('key', $key)->first();
	}

	public function key(int $user_id, string $key = '*') {
		$prefs = static::where('user_id', $user_id);

		if ($key === '*')
			return $prefs->get();
		else
			return $prefs->where('key', $key)->first();
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
			}
		}

		return $value;
	}

	public function setAttribute($key, $value) {
		if ($key === 'value') {
			switch (gettype($value)) {
				case 'string':
					$type = 'STRING';
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
			}

			parent::setAttribute('type', $type);
		}

		return parent::setAttribute($key, $value);
	}

	public function toArray($all = false) {
		return $all ? parent::toArray() : [
			$this->key => $this->value,
		];
	}

	public function toJson($all = false) {
		return json_encode($all ? parent::toArray() : [
			$this->key => $this->value,
		]);
	}

	public function user() {
		return $this->belongsTo('App\Models\User');
	}
}
