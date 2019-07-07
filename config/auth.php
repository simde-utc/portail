<?php
/**
 * Authentication systems configuration file
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

return [
    'services' => [
        'cas' => [
            'name' => 'CAS-UTC',
            'description' => 'Connexion (CAS) réservée aux membres UTC',
            'class' => App\Services\Auth\Cas::class,
            'model' => App\Models\AuthCas::class,
            'loggable' => true,
            'registrable' => false,
        ],

        'password' => [
            'name' => 'Mot de passe',
            'description' => 'Connexion par adresse email et mot de passe (possibilité de créer un compte)',
            'class' => App\Services\Auth\Password::class,
            'model' => App\Models\AuthPassword::class,
            'loggable' => true,
            'registrable' => true,
        ],

        'app' => [
            'name' => 'Application',
            'description' => 'Connexion pour l\'application',
            'class' => App\Services\Auth\App::class,
            'model' => App\Models\AuthApp::class,
            'loggable' => false,
            'registrable' => false,
        ],
    ],

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
            'table' => 'users'
        ],
        'api' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
            'table' => 'users'
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'auth_passwords_resets',
            'expire' => 60,
        ],
    ],
];
