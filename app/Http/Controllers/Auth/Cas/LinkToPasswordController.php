<?php

namespace App\Http\Controllers\Auth\Cas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
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

            \Auth::guard('cas')->user()->update([
                'email' => $request->input('email')
            ]);

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

                // On actualise les données de l'utilisateur avec le cas
                $user = \Auth::guard('web')->user();
                $user->firstname = $casUser->firstname;
                $user->lastname = $casUser->lastname;

                // On respécifie notre connexion via CAS
                Session::updateOrCreate(['id' => \Session::getId()], ['auth_provider' => 'cas']);

                // On se déconnecte du mode cas et on supprime l'utilisateur inutile
                \Auth::guard('cas')->logout();

                try {
                    $casUser->delete();
                } catch (QueryException $e) {
                    \Session::flash('error', 'Vos deux comptes possèdent une activité antérieure et il n\'est donc pas possible de les lier. Contactez le SiMDE pour réaliser cette tâche et veuillez actualiser pour continuer');
                    \Session::flash('old', ['email' => $request->input('email')]);

                    return view('auth.cas.link');
                }

                $cas->save();
                $user->save();

                return $redirect;
            }
            else
                return view('auth.cas.link');
        }
    }
}
