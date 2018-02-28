<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthCas extends Model
{
	protected $fillable = [
	 'user_id', 'login', 'email', 'last_login_at',
	];

	protected $primaryKey = 'user_id';
}
