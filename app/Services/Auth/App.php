<?php

namespace App\Services\Auth;

use Ginger;
use App\Models\User;
use App\Models\AuthApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class App extends BaseAuth
{
	protected $name = 'app';

	public function __construct() {
		$this->config = config("auth.services.".$this->name);
	}

	/**
	 * Crée la connexion auth
	 */
	public function addAuth($user_id, array $info) {
		return AuthApp::create([
			'user_id' => $user_id,
			'app_id' => $info['app_id'],
			'password' => Hash::make($info['password']),
			'key' => str_random(64)
		]);
	}
}
