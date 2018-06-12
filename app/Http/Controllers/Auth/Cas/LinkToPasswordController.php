<?php

namespace App\Http\Controllers\Auth\Cas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Auth\Password;
use App\Models\Session;

class LinkToPasswordController extends Controller
{
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:cas');
    }

    public function index(Request $request) {
        return view('auth.cas.link');
    }

    public function store(Request $request) {
        if ($request->filled('password_confirmation')) {
            (new Password)->addAuth(\Auth::guard('cas')->id(), $request->input());

            \Auth::guard('web')->login(\Auth::guard('cas')->user());
            \Auth::guard('cas')->logout();

            return redirect(\Session::get('url.intended', '/'));
        }
        else {
            $redirect = (new Password)->login($request);

            if (\Auth::guard('web')->check()) {
                $casUser = \Auth::guard('cas')->user();

                // On réaffecte notre CAS à notre ancien compte
                $cas = $casUser->cas;
                $cas->user_id = \Auth::guard('web')->id();
                $cas->save();

                // On actualise les données de l'utilisateur avec le cas
                $user = \Auth::guard('web')->user();
                $user->firstname = $casUser->firstname;
                $user->lastname = $casUser->lastname;
                $user->save();

                // On se déconnecte du mode cas et on supprime l'utilisateur inutile
                \Auth::guard('cas')->logout();
                $casUser->delete();

                // On respécifie notre connexion via CAS
                Session::updateOrCreate(['id' => \Session::getId()], ['auth_provider' => 'cas']);

                return $redirect;
            }
            else
                return view('auth.cas.link');
        }
    }
}
