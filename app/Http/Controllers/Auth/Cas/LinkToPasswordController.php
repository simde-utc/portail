<?php

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware(['auth:web', 'user:cas', 'user:!password']);
    }

    public function index(Request $request) {
        return view('auth.cas.link');
    }

    public function store(Request $request) {
        (new Password)->addAuth(\Auth::id(), $request->input());

        \Auth::user()->update([
            'email' => $request->input('email')
        ]);

        return redirect(\Session::get('url.intended', '/'));
    }
}
