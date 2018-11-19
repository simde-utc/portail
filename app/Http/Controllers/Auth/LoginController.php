<?php
/**
 * Gère la connexion via un formulaire.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
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
use App\Models\Session;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Où rediriger les connectés.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Uniquement les non-connectés peuvent se connecter à leur compte.
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
     * Affiche la vue de choix de méthode de login.
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
     * Connection de l'utilisateur après passage par l'API.
     *
     * @param  Request $request
     * @param  string  $provider Type d'authentification.
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
     * Récupère la classe d'authentication $provider_class dans le service container de Laravel et applique le show login.
     *
     * @param  Request $request
     * @param  string  $provider Type d'authentification.
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
     * Actualisation du captcha.
     *
     * @param  Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        return response()->json(['captcha' => captcha_img()]);
    }

    /**
     * Déconnection de l'utilisateur.
     *
     * @param  Request $request
     * @param  string  $redirect Url de redirection.
     * @return mixed
     */
    public function destroy(Request $request, string $redirect=null)
    {
        $service = config("auth.services.".Session::find(\Session::getId())->auth_provider);
        $redirect = $service === null ? null : resolve($service['class'])->logout($request);

        if ($redirect === null) {
            $redirectUrl = $request->query('redirect', url()->previous());

            // Évite les redirections sur logout.
            if ($redirectUrl && $redirectUrl !== $request->url()) {
                $redirect = redirect($redirectUrl);
            } else {
                $redirect = redirect('welcome');
            }
        }

        // Ne pas oublier de détruire sa session.
        \Session::flush();

        // On le déconnecte uniquement lorsque le service a fini son travail.
        Auth::logout();

        return $redirect;
    }
}
