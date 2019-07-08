<?php
/**
 * Admin homepage.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use App\Admin\Models\Menu;
use Encore\Admin\Widgets\Box;

class HomeController extends Controller
{
    /**
     * Display of the welcome page.
     *
     * @param  Content $content
     * @return mixed
     */
    public function index(Content $content)
    {
        return $content
            ->header('SiMDE')
            ->description('Il y a une vie après les cours')
            ->row(new Box('Bienvenue', view('admin.home.welcome')))
            ->row(new Box('Accès rapide', view('admin.home.index')));
    }
}
