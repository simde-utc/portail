<?php
/**
 * Gère la connexion d'un utilisateur.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Client;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Services\Auth\AuthService;
use App\Models\Session;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Liste les moyens de connexion.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if (\Auth::check()) {
            return $this->alreadyConnected();
        }

        $services = config('auth.services');
        $auth = [];

        foreach ($services as $provider => $service) {
            $auth[$provider] = [
                'name'         => $service['name'],
                'description'  => $service['description'],
                'login_url'    => $service['loggable'] ? route('login.show', ['provider' => $provider]) : null,
                'register_url' => $service['registrable'] ? route('register.show', ['provider' => $provider]) : null,
            ];
        }

        return response()->json($auth, 200);
    }

    /**
     * Déconnecte l'utilisateur du portail et le renvoie sur la route de déconnexion de sa méthode de connexion.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $token = $request->user()->token();
        $session_id = $token->session_id;
        $service = config('auth.services.'.(Session::find($session_id)->auth_provider));
        $redirect = $service === null ? null : resolve($service['class'])->logout($request);

        if ($redirect === null) {
            // On le déconnecte uniquement lorsque le service a fini son travail.
            Session::find($session_id)->update([
                'user_id'       => null,
                'auth_provider' => null,
            ]);

            return response()->json(['message' => 'Utilisateur déconnecté avec succès'], 202);
        } else {
            return response()->json(['redirect' => route('logout')], 200);
        }
    }
}
