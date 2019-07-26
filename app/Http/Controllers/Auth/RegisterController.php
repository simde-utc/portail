<?php
/**
 * Manage inscription via form.
 *
 * @author Natan Danous <natan.danous@gmail.com>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Romain Maliach-Auguste <r.maliach@live.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\AuthPassword;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirict users once they are logged in.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Only users that aren't currently signed in can create an account.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Fields required for form validation.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            // 'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Display the form for a specific authentification type.
     * Redirect to the login page if non-existant.
     *
     * @param  Request $request
     * @param  string  $provider Authentification type.
     * @return mixed
     */
    public function show(Request $request, string $provider=null)
    {
        $config = config("auth.services.$provider");

        if ($config === null || !$config['registrable']) {
            return redirect()->route('login.show')->cookie('auth_provider', '', config('portail.cookie_lifetime'));
        } else {
            return resolve($config['class'])->showRegisterForm($request);
        }
    }

    /**
     * Register the user for a specific type of authentification.
     * Redirect to login page if non-existant.
     *
     * @param  Request $request
     * @param  string  $provider Authentification type.
     * @return mixed
     */
    public function store(Request $request, string $provider)
    {
        $config = config("auth.services.$provider");

        if ($config === null || !$config['registrable']) {
            return redirect()->route('register.show')->cookie('auth_provider', $provider, config('portail.cookie_lifetime'));
        } else {
            setcookie('auth_provider', $provider, config('portail.cookie_lifetime'));

            return resolve($config['class'])->register($request);
        }
    }
}
