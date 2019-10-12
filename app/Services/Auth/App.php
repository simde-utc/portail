<?php
/**
 * Application authentification service.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Services\Auth;

use Ginger;
use App\Models\User;
use App\Models\AuthApp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class App extends BaseAuth
{
    protected $name = 'app';

    /**
     * Configuration retrievement.
     */
    public function __construct()
    {
        $this->config = config("auth.services.".$this->name);
    }

    /**
     * Auth connection creation.
     *
     * @param string $user_id
     * @param array  $info
     * @return AuthApp
     */
    public function addAuth(string $user_id, array $info)
    {
        return AuthApp::create([
            'user_id' => $user_id,
            'app_id' => $info['app_id'],
            'password' => Hash::make($info['password']),
            'key' => Str::random(64)
        ]);
    }
}
