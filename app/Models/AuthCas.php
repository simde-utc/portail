<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthCas extends Model
{
	protected $fillable = [
	 'user_id', 'login', 'updated_at'
	];

	protected $primaryKey = 'user_id';
}
