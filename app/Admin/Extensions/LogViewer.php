<?php

namespace App\Admin\Extensions;

use Encore\Admin\LogViewer\LogViewer as BaseLogViewer;

class LogViewer extends BaseLogViewer
{
    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        static::registerRoutes();

        parent::createMenu('Log viewer', 'logs', 'fa-database');
    }
}
