<?php
/**
 * Connexion entre le compte CAS et la connexion email/mdp.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\Auth\Cas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Services\Auth\Password;
use App\Models\Session;

class LinkToPasswordController extends Controller
{
    protected $redirectTo = '/';

    /**
     * Définition des middlewares: utilisateur cas et on password.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:web', 'user:cas', 'user:!password']);
    }

    /**
     * Renvoie la page de linkage.
     *
     * @param  Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return view('auth.cas.link');
    }

    /**
     * Enregistre l'interconnexion des modes de connexion.
     *
     * @param  Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        (new Password)->addAuth((string) \Auth::id(), $request->input());

        \Auth::user()->update([
            'email' => $request->input('email'),
        ]);

        return redirect(\Session::get('url.intended', '/'));
    }
}
