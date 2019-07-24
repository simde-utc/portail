<?php
/**
 * Handle old portal's routes.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class OldToNewPortailController extends Controller
{
    /**
     * /asso redirection.
     *
     * @return mixed
     */
    public function asso()
    {
        return redirect('assos', 301);
    }

    /**
     * /asso/{login} redirection.
     *
     * @param string $login
     * @return mixed
     */
    public function assoLogin(string $login)
    {
        return redirect('assos/'.$login, 301);
    }

    /**
     * /asso/articles/{login} redirection.
     *
     * @param string $login
     * @return mixed
     */
    public function assoArticlesLogin(string $login)
    {
        return redirect('assos/'.$login.'/articles', 301);
    }

    /**
     * /article redirection.
     * /article/show/{wathever} redirection.
     *
     * @return mixed
     */
    public function article()
    {
        return redirect('articles', 301);
    }

    /**
     * /event redirection.
     * /event/calendar redirection.
     * /event/show/{wathever} redirection.
     *
     * @return mixed
     */
    public function event()
    {
        return redirect('events', 301);
    }
}
