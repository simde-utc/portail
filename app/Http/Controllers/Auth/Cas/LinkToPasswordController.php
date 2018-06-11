<?php

namespace App\Http\Controllers\Auth\Cas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LinkToPasswordController extends Controller
{
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
}
