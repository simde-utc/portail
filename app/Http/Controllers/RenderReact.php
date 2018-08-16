<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class RenderReact extends Controller {
	public function __invoke($whatever = null) {
		return view('react');
	}
}