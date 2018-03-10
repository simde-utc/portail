<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthPassword extends Model
{
		public $incrementing = false;			// L'id n'est pas autoincrementé
		protected $primaryKey = 'user_id';

		protected $fillable = [
		 	'user_id', 'last_login_at',
		];

		protected $hidden = [
				'password',
		];

		public function user() {
				return $this->belongsTo('App\Models\User');
		}
}
