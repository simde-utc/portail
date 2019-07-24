<?php
/**
 * Manage a user connection.
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

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * List connection means.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if (\Auth::check()) {
            abort(400, 'Déjà connecté !');
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
     * Disconnect the user from the portal and redirects to the logout route of its connection method.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $service = config('auth.services.'.\Session::get('auth_provider'));
        $redirect = $service === null ? null : resolve($service['class'])->logout($request);

        if ($redirect === null) {
            return response()->json(['message' => 'Utilisateur déconnecté avec succès'], 202);
        } else {
            return response()->json(['redirect' => route('logout')], 200);
        }
    }
}
