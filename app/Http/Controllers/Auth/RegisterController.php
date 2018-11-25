<?php
/**
 * Gère l'inscription via un formulaire.
 *
 * @author Natan Danous <natan.danous@gmail.com>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
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
     * Où rediriger les connectés.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Uniquement les non-connectés peuvent créer un compte.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Champs requis pour la validation du formulaire.
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
     * Montre le formulaire spécifique à un type d'authentification.
     * Redirige vers la page de connexion si non existant.
     *
     * @param  Request $request
     * @param  string  $provider Type d'authentification.
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
     * Enregistrement de l'utilisateur pour un type d'authentification.
     * Redirige vers la page de connexion si non existant.
     *
     * @param  Request $request
     * @param  string  $provider Type d'authentification.
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
