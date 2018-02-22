<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CAS;

class CasController extends Controller
{
    /*
        Renvoie vers la page de login cas si pas de ticket.
        Si ticket, retourne processUser().
    */
    public function showLoginForm(Request $request) {
        if(!empty($request->query()))
            return $this->processUser($request);
        return CAS::login(route('cas'));
    }

    /*
        Authentifie l'utilisateur et redirige vers home.
    */
    public function processUser(Request $request) {
        $user = CAS::authenticate(route('cas'), $request->query('ticket'));
        if ($user == -1)
            return redirect()->route('cas');

        return redirect()->route('home');
    }
}