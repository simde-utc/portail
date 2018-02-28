<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Services\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public function showLoginOptions()
    {
        return view('login.index');
    }

    /*
        Renvoie vers la page de login cas si pas de ticket.
        Si ticket, retourne processUser().
    */
    public function showCasLoginForm(Request $request) {
        if(!empty($request->query()))
            return $this->processUser($request);
        return Auth\Cas::login(route('login.cas'));
    }

    public function showPassLoginForm() {
        return view('auth.login');
    }

    /*
        A METTRE AUTRE PART.
        Authentifie l'utilisateur et redirige vers home.
    */
    public function processUser(Request $request) {
        return redirect()->route(Auth\Cas::authenticate(route('login.cas'), $request->query('ticket')) ? 'home' : 'login.cas');
    }
}
