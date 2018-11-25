<?php
/**
 * Permet de renvoyer la page React.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class RenderReact extends Controller
{
    /**
     * Gère n'importe quelle requête.
     * @param  string $whatever Catch n'importe quelle valeur.
     * @return mixed
     */
    public function __invoke(string $whatever=null)
    {
        return view('react');
    }
}
