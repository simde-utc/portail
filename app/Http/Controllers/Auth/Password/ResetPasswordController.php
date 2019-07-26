<?php
/**
 * Manage the reset of passwords.
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
     * Where to redirect users once they're logged in.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Only users that aren't connected can reset their password.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the password reset form.
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
     * Reset the password.
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
