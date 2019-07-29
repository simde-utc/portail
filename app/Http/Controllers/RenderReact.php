<?php
/**
 * Render the React page
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Romain Maliach-Auguste <r.maliach@live.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class RenderReact extends Controller
{
    /**
     * Manage any request
     * @param  string $whatever Catches any value.
     * @return mixed
     */
    public function __invoke(string $whatever=null)
    {
        return view('react');
    }
}
