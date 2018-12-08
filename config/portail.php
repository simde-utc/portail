<?php
/**
 * Fichier de configuration du Portail.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

$roleToPermission = [
    'president' => [
        'access',
    ],
    'resp logistique' => [
        'access',
    ],
];

return [
	// Gestion des versions du Portail.
    'versions' => [
        'v1'
    ],

    // Version actuelle du serveur (en dessous: déprécié, au dessus: en beta).
    'version' => 'v0',

    // Les headers spécifiques au Portail (commençant par X-Portail).
    'headers' => [
        'warn'			=> 'X-Portail-Warn',
        'version'		=> 'X-Portail-Version',
    ],

    // Définition des rôles.
    'roles' => [
    // Roles admins.
        'admin' => [
            'users' => 'admin',
            'assos' => 'president',
            'groups' => 'group admin',
        ],
        'assos' => [
            env('APP_ASSO', 'simde') => [
                'president' => 'superadmin',
                'bureau' => 'admin',
            ],
            'bde' => [
                'president' => 'superadmin',
            ]
        ],
    ],

    'permissions' => [
        'assos' => [
            'bde' => $roleToPermission,
            'poleae' => $roleToPermission,
            'polesec' => $roleToPermission,
            'polete' => $roleToPermission,
            'polevdc' => $roleToPermission,
        ],
    ],

    'reservations' => [
        'max_duration' => 2,
    ],

    'cas' => [
        'url' => env('CAS_URL', ''),
        'image' => env('CAS_IMAGE', ''),
    ],

    'ginger_key' => env('GINGER_KEY', ''),

    'cookie_lifetime' => 518400,
];
