<?php
/**
 * Dashboard for admins.
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

class DashboardController extends Controller
{
    /**
     * Give access only if the user has the right permission.
     */
    public function __construct()
    {
        $this->middleware('permission:admin');
    }

    /**
     * Dashboard's display.
     *
     * @param  Content $content
     * @return mixed
     */
    public function index(Content $content)
    {
        return $content
            ->header('Dashboard')
            ->description('Dashboard du SiMDE')
            ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }
}
