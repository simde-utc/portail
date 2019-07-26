<?php
/**
 * Manage connexions via form.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Romain Maliach-Auguste <r.maliach@live.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\AuthService;
use App\Services\Auth\Cas;
use Laravel\Passport\Token;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users once they are connected.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Only users that aren't currently logged in can log in.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'destroy']);
        $this->middleware('auth:web', ['only' => 'destroy']);

        if ($url = \URL::previous()) {
            \Session::put('url.intended', $url);
        }
    }

    /**
     * Show the login method choice view.
     *
     * @param  Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $provider = (string) $request->cookie('auth_provider');
        $provider_class = config('auth.services.'.$provider.'.class');

        if ($provider_class === null || $request->query('see') === 'all') {
            return view('login.index');
        } else {
            return redirect()->route('login.show', ['provider' => $provider]);
        }
    }

    /**
     * User login after API call.
     *
     * @param  Request $request
     * @param  string  $provider Authentification type.
     * @return mixed
     */
    public function store(Request $request, string $provider)
    {
        $provider_class = config("auth.services.$provider.class");

        if ($provider_class === null) {
            return redirect()->route('login.show');
        } else {
            return resolve($provider_class)->login($request)
                    ->cookie('auth_provider', $provider, config('portail.cookie_lifetime'));
        }
    }

    /**
     * Fetche the authentication class $provider_class in the Laravel service container de Laravel, and shows login.
     *
     * @param  Request $request
     * @param  string  $provider Authentification type.
     * @return mixed
     */
    public function show(Request $request, string $provider)
    {
        $provider_class = config("auth.services.$provider.class");

        if ($provider_class === null) {
            return redirect()->route('login')->cookie('auth_provider', $provider, config('portail.cookie_lifetime'));
        } else {
            return resolve($provider_class)->showLoginForm($request);
        }
    }

    /**
     * Captcha actualisation
     *
     * @param  Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        return response()->json(['captcha' => captcha_img()]);
    }

    /**
     * User logout.
     *
     * @param  Request $request
     * @param  string  $redirect Url de redirection.
     * @return mixed
     */
    public function destroy(Request $request, string $redirect=null)
    {
        $service = config("auth.services.".session('auth_provider'));
        $redirect = $service === null ? null : resolve($service['class'])->logout($request);

        if ($redirect === null) {
            $redirectUrl = $request->query('redirect', url()->previous());

            // Avoids redirections to logout.
            if ($redirectUrl && $redirectUrl !== $request->url()) {
                $redirect = redirect($redirectUrl);
            } else {
                $redirect = redirect('welcome');
            }
        }

        // If impersonate mode is active.
        if ($user = Auth::guard('admin')->user()) {
            if (Auth::guard('web')->id() !== $user->id) {
                Auth::guard('web')->login($user);

                return redirect('/');
            }
        }

        // Session destruction.
        session()->flush();

        // Logout once the service has finished its job.
        Auth::logout();

        return $redirect;
    }
}
