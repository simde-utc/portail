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
	 	'user_id', 'app_id', 'password', 'key',
	];

	protected $hidden = [
		'password',
	];

	protected $must = [
		'user_id', 'app_id',
	];

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function getUserByIdentifiant($app_id) {
        $app = static::where('app_id', $app_id)->first();

        if ($app) {
            $user = $app->user;
            $user->app = $app;

            return $user;
        }
        else
            return null;
    }

	public function isPasswordCorrect($password) {
        return Hash::check($password, $this->password);
	}
}
