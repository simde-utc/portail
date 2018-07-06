<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasKeyValue;

class UserPreference extends Model
{
	use HasKeyValue;

	public $incrementing = false; // L'id n'est pas autoincrementé
	protected $table = 'users_preferences';
	protected $primaryKey = ['user_id', 'key', 'only_for'];
	protected $fillable = [
		'user_id', 'key', 'value', 'type', 'only_for',
	];

	public function scopeOnlyFor($query, $only_for) {
		return $query->where('only_for', $only_for);
	}
}
