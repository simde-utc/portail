<?php

namespace App\Models;

use App\Traits\Model\HasKeyValue;

class UserPreference extends Model
{
	use HasKeyValue;

	public $incrementing = false; // L'id n'est pas autoincrementé

	protected $table = 'users_preferences';

	protected $primaryKey = [
		'user_id', 'key', 'only_for',
	];

	protected $fillable = [
		'user_id', 'key', 'value', 'type', 'only_for',
	];

	public function hideData(array $params = []): Model {
		return $this; // TODO
	}

	public function scopeOnlyFor($query, $only_for) {
		return $query->where('only_for', $only_for);
	}
}
