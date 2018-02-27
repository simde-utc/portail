<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthPassword extends Model
{
	protected $fillable = [
	 'user_id',
	];

	protected $hidden = [
		'password',
	];

	protected $primaryKey = 'user_id';
}
