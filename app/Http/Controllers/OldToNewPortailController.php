<?php
/**
 * Permet de gérer les anciennes routes du Portail.
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
     * Redirection de /asso.
     *
     * @return mixed
     */
    public function asso()
    {
        return redirect('assos', 301);
    }

    /**
     * Redirection de /asso/{login}.
     *
     * @param string $login
     * @return mixed
     */
    public function assoLogin(string $login)
    {
        return redirect('assos/'.$login, 301);
    }

    /**
     * Redirection de /asso/articles/{login}.
     *
     * @param string $login
     * @return mixed
     */
    public function assoArticlesLogin(string $login)
    {
        return redirect('assos/'.$login.'/articles', 301);
    }

    /**
     * Redirection de /article.
     * Redirection de /article/show/{wathever}.
     *
     * @return mixed
     */
    public function article()
    {
        return redirect('articles', 301);
    }

    /**
     * Redirection de /event.
     * Redirection de /event/calendar.
     * Redirection de /event/show/{wathever}.
     *
     * @return mixed
     */
    public function event()
    {
        return redirect('events', 301);
    }
}
