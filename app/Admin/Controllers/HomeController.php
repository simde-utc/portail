<?php
/**
 * Page admin d'accueil.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    /**
     * Affichage de la page de bienvenue.
     *
     * @param  Content $content
     * @return mixed
     */
    public function index(Content $content)
    {
        return $content
            ->header('SiMDE')
            ->description('Il y a une vie après les cours');
    }
}
