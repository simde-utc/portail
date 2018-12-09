<?php

return [
    'name' => 'Portail admin',

    'logo' => '<b>Portail</b> admin',

    'logo-mini' => '<b>Pa</b>',

    'route' => [

        'prefix' => 'admin',

        'namespace' => '\\App\\Admin\\Controllers',

        'middleware' => ['web', 'admin-portail'],
    ],

    'directory' => app_path('Admin'),

    'title' => 'Portail - Admin',

    'https' => env('ADMIN_HTTPS', false),

    'auth' => [

        'controller' => App\Admin\Controllers\AuthController::class,

        'guards' => [
            'admin' => [
                'driver'   => 'session',
                'provider' => 'admin',
            ],
        ],

        'providers' => [
            'admin' => [
                'driver' => 'eloquent',
                'model'  => App\Admin\Models\Admin::class,
            ],
        ],
    ],

    'upload' => [

        // Disk in `config/filesystem.php`.
        'disk' => 'admin',

        // Image and file upload path under the disk above.
        'directory' => [
            'image' => 'images',
            'file'  => 'files',
        ],
    ],
    'database' => [
        'connection' => '',

        'users_table' => 'users',
        'users_model' => App\Admin\Models\Admin::class,

        'roles_model' => App\Admin\Models\Role::class,
        'permissions_model' => App\Admin\Models\Permission::class,

        'menu_table' => 'admin_menu',
        'menu_model' => App\Admin\Models\Menu::class,

        'operation_log_table'    => 'admin_operation_log',
        'role_menu_table'        => 'admin_role_menu',
    ],

    'operation_log' => [
        'enable' => true,

        'allowed_methods' => ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'],

        'except' => [
            'admin/auth/logs*',
        ],
    ],

    'map_provider' => 'google',

    'skin' => 'skin-black',

    'layout' => ['sidebar-mini', 'sidebar-collapse'],

    'login_background_image' => '',

    'show_version' => true,

    'show_environment' => false,

    'menu_bind_permission' => true,

    'enable_default_breadcrumb' => true,

    'extension_dir' => app_path('Admin/Extensions'),

    'extensions' => [
        'search' => [
            'limit' => 5,
        ],

        'api-tester' => [
            'prefix' => 'api',

            'guard'  => 'api',

            'class' => \App\Admin\Extensions\ApiTester::class,
        ],

        'chartjs' => [
            'enable' => true,
        ]
    ],
];
