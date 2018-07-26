<?php

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
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
			'firstname' => 'required|string|max:255',
			'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            //'password' => 'required|string|min:6|confirmed',
        ]);
    }

	public function show(Request $request, string $provider) {
		$config = config("auth.services.$provider");

		if ($config === null || !$config['registrable'])
			return redirect()->route('register.show', ['redirect' => \Session::get('url.intended', $request->query('redirect', url()->previous()))])->cookie('auth_provider', '', config('portail.cookie_lifetime'));
		else
			return resolve($config['class'])->showRegisterForm($request);
	}

	/**
	 * Enregistrement de l'utilisateur après passage par l'API
	 */
	public function store(Request $request, $provider) {
		$config = config("auth.services.$provider");

		if ($config === null || !$config['registrable'])
			return redirect()->route('register.show', ['redirect' => \Session::get('url.intended', $request->query('redirect', url()->previous()))])->cookie('auth_provider', $provider, config('portail.cookie_lifetime'));
		else {
			setcookie('auth_provider', $provider, config('portail.cookie_lifetime'));

			return resolve($config['class'])->register($request);
		}
	}
}
