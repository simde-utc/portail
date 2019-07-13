<?php
/**
 * Password authentification service.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Services\Auth;

use Ginger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Password extends BaseAuth
{
    protected $name = 'password';

    /**
     * Configuration retrievement.
     */
    public function __construct()
    {
        $this->config = config("auth.services.".$this->name);
    }

    /**
     * Connexion method.
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        if ($request->input('email') && $request->input('password')) {
            $user = User::where('email', $request->input('email'))->first();

            if ($user !== null) {
                $userAuth = $user->password()->first();

                if ($userAuth !== null && Hash::check($request->input('password'), $userAuth->password)) {
                    return $this->connect($request, $user, $userAuth);
                }
            }

            return $this->error($request, null, null, 'L\'adresse email et/ou le mot de passe est incorrect');
        } else {
            return $this->show($request);
        }
    }

    /**
     * Subcribing method.
     *
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request)
    {
        $request->validate(...$this->getValidations());

        return $this->create($request, [
            'email' => $request->input('email'),
            'firstname' => $request->input('firstname'),
            'lastname' => strtoupper($request->input('lastname')),
        ], [
            'password' => Hash::make($request->input('password')),
        ]);
    }

    /**
     * Creates the auth connection.
     *
     * @param string $user_id
     * @param array  $info
     * @return \App\Models\Auth
     */
    public function addAuth(string $user_id, array $info)
    {
        list($validation, $validationText) = $this->getValidations();
        $info['password_confirmation'] = ($info['password_confirmation'] ?? $info['password']);

        Validator::make($info, [
            'email' => $validation['email'],
            'password' => $validation['password'],
        ], $validationText)->validate();

        $info['password'] = Hash::make($info['password']);

        return parent::addAuth($user_id, $info);
    }

    /**
     * Redirects to the right page in case of success.
     *
     * @param Request          $request
     * @param User             $user
     * @param \App\Models\Auth $userAuth
     * @param string           $message
     * @return mixed
     */
    protected function success(Request $request, User $user=null, \App\Models\Auth $userAuth=null, string $message=null)
    {
        $casAuth = $user->cas;

        if ($casAuth !== null && $casAuth->is_active && !Ginger::userExists($casAuth->login)) {
            // If the user no longer exists in Ginger, we can disable his account.
            $casAuth->is_active = 0;
            $casAuth->save();

            return parent::success($request, $user, $userAuth, 'Vous êtes maintenant considéré.e comme un.e Tremplin');
        } else {
            return parent::success($request, $user, $userAuth, $message);
        }
    }

    /**
     * Retrieves validations for this authentification.
     *
     * @return array
     */
    protected function getValidations()
    {
        $birthdate = \Carbon\Carbon::now()->subYears(16)->toDateString();

        return [
            [
                'email' => ['required', 'email', 'regex:#.*(?<!utc\.fr|escom\.fr)$#', 'unique:users'],
                'firstname' => 'required|regex:#^[\pL\s\-]+$#u',
                'lastname' => 'required|regex:#^[\pL\s\-]+$#u',
                'password' => 'required|confirmed|regex:#^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$#',
                'birthdate' => 'required|date_format:Y-m-d|before_or_equal:'.$birthdate,
                'captcha' => 'required|captcha'
            ], [
                'email.unique' => 'L\'adresse email est déjà utilisée',
                'email.regex' => 'Il n\'est pas possible de s\'enregistrer avec une adresse email utc.fr ou escom.fr
					(Veuillez vous connecter via CAS-UTC)',
                'password.regex' => 'Le mot de passe doit avoir au moins: 8 caractères, une lettre en minuscule,
					une lettre en majuscule, un chiffre',
                'captcha.captcha' => 'Le Captcha est invalide',
            ]
        ];
    }
}
