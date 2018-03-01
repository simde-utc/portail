<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthCas extends Model
{
	public $incrementing = false;			// L'id n'est pas autoincrementé
	protected $primaryKey = 'user_id';

	protected $fillable = [
	 'user_id', 'login', 'email', 'last_login_at',
	];

	public function user() {
		return $this->belongsTo('App\Models\User');
	}
}
