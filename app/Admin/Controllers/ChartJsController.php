<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use App\Models\User;

class ChartJsController extends Controller
{
    public function index(Content $content)
    {
        $dates = [];
        $nbr = 0;

        foreach (User::orderBy('created_at')->get(['created_at']) as $user) {
            $dates[$user->created_at->format('d-m-Y H:m:s')] = ++$nbr;
        }

        return $content
            ->header('Chartjs')
            ->body(new Box('Utilisateurs', view('admin.chartjs.users', ['data' => $dates])));
    }
}
