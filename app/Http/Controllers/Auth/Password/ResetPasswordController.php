<?php
/**
 * Gère la réinitialisation des mots de passe.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\Auth\Password;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use \Illuminate\Contracts\Auth\CanResetPassword;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Où rediriger les connectés.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Uniquement les non-connectés peuvent réinitialiser un mot de passe.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Affiche le formulaire de réinitialisation.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string|null              $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, string $token=null)
    {
        return view('auth.password.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Réinitilise le mot de passe.
     *
     * @param  CanResetPassword $user
     * @param  string           $password
     * @return void
     */
    protected function resetPassword(CanResetPassword $user, string $password)
    {
        $user->setRememberToken(Str::random(60));
        $user->save();

        $auth = $user->password;
        $auth->password = Hash::make($password);
        $auth->save();

        event(new PasswordReset($user));

        $this->guard()->login($user);
    }
}
