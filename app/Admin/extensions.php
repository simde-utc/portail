<?php

$menuModel = config('admin.database.menu_model');

$lastOrder = $menuModel::max('order');

$menuModel::create([
    'parent_id' => 0,
    'order'     => ++$lastOrder,
    'title'     => 'Rechercher',
    'icon'      => 'fa-search',
    'uri'       => 'search',
    'permission'=> 'search'
]);

$menuModel::create([
    'parent_id' => 0,
    'order'     => ++$lastOrder,
    'title'     => 'Utilisateurs',
    'icon'      => 'fa-users',
    'uri'       => 'users',
    'permission'=> 'user'
]);

App\Admin\Extensions\ApiTester::import();
App\Admin\Extensions\ChartJs::import();
App\Admin\Extensions\LogViewer::import();
