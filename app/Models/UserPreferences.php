<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreferences extends Model
{
		protected $table = 'users_preferences';
		protected $primaryKey = 'user_id';

		protected $fillable = [
				'user_id', 'email',
		];

		public function user() {
				return $this->belongsTo('App\Models\User');
		}
}
