<?php
/**
 * Affiche des graphes.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use App\Models\User;

class ChartsController extends Controller
{
    /**
     * Affiche un tas de graphes.
     *
     * @param  Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $dates = [];
        $nbr = 0;

        foreach (User::orderBy('created_at')->get(['created_at']) as $user) {
            $dates[$user->created_at->format('d-m-Y H:m:s')] = ++$nbr;
        }

        return $content
            ->header('Charts')
            ->body(new Box('Utilisateurs', view('admin.charts.users', ['data' => $dates])));
    }
}
