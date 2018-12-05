<?php

namespace App\Admin\Extensions;

use Encore\Chartjs\Chartjs as BaseChartJs;

class ChartJs extends BaseChartJs
{
    /**
     * Register routes for laravel-admin.
     *
     * @return void
     */
    protected static function registerRoutes()
    {
        parent::routes(function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->get('charts', 'App\Admin\Controllers\ChartJsController@index')->name('charts-index');
        });
    }

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        static::registerRoutes();

        parent::createMenu('Charts', 'charts', 'fa-area-chart');
    }
}
