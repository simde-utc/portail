<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthPassword extends Model
{
	protected $fillable = [
	 'user_id', 'last_login_at',
	];

	protected $hidden = [
		'password',
	];

	protected $primaryKey = 'user_id';
}
