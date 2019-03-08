<?php
/**
 * Fichier de configuration de la base de données.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Cesar Richard <cesar.richard2@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'database'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'portail'),
            'username' => env('DB_USERNAME', 'portail'),
            'password' => env('DB_PASSWORD', 'portail'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        'old-portail' => [
            'driver' => env('OLD_DB_CONNECTION', 'mysql'),
            'host' => env('OLD_DB_HOST', '127.0.0.1'),
            'port' => env('OLD_DB_PORT', '3306'),
            'database' => env('OLD_DB_DATABASE', 'forge'),
            'username' => env('OLD_DB_USERNAME', 'forge'),
            'password' => env('OLD_DB_PASSWORD', ''),
            'unix_socket' => env('OLD_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
    ],

    'migrations' => 'migrations',

    'redis' => [
        'client' => 'predis',
        'default' => [
            'host' => env('REDIS_HOST', 'redis'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],
    ],
];
