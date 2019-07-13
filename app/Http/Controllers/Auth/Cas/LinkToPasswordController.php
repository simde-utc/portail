<?php
/**
 * Link between CAS auth and email/pwd auth.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Romain Maliach-Auguste <r.maliach@live.fr>
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
     * Middleware definition: CAS, password.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:web', 'user:cas', 'user:!password']);
    }

    /**
     * Returns linking page.
     *
     * @param  Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return view('auth.cas.link');
    }

    /**
     * Stores the link between auth types.
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
