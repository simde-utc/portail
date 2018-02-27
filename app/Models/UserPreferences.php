<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreferences extends Model
{
	protected $fillable = [
	 'user_id', 'email',
	];

  protected $table = 'users_preferences';
	protected $primaryKey = 'user_id';
}
