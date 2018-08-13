<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use App\Traits\Model\HasHiddenData;
use NastuzziSamy\Laravel\Traits\HasSelection;

class AuthApp extends Auth // TODO must
{
    use HasHiddenData, HasSelection;

    public $incrementing = false;

	protected $fillable = [
	 	'user_id', 'app_id', 'key',
	];

	protected $hidden = [
		'key',
	];

	protected $must = [
		'user_id', 'app_id',
	];

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function getUserByIdentifiant($app_id) {
		return static::where('app_id', $app_id)->first();
    }

	public function isPasswordCorrect($key) {
		return $this->key = $key;
	}
}
