<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasKeyValue;

class UserPreference extends Model
{
	use HasKeyValue;

	protected $table = 'users_preferences';
	protected $primaryKey = 'user_id';
	protected $fillable = [
		'user_id', 'key', 'value', 'type',
	];
}
