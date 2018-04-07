<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model {
	protected $fillable = [
		'id', 'user_id', 'auth_provider', 'ip_address', 'user_agent', 'payload', 'last_activity',
	];
}
