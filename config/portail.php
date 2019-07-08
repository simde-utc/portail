<?php
/**
 * Portal configuration file.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

$roleToPermission = [
    'president' => [
        'handle-access',
    ],
    'resp logistique' => [
        'handle-access',
    ],
];

return [
	// Portal version management.
    'versions' => [
        'v1'
    ],

    // Current servers's version (lower: deprecated, higher: beta).
    'version' => 'v0',

    // Specific headers (starting with X-Portail).
    'headers' => [
        'warn'			=> 'X-Portail-Warn',
        'version'		=> 'X-Portail-Version',
    ],

    // Roles definition.
    'roles' => [
    // Admin Roles.
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

    'bookings' => [
        'max_duration' => 3,
    ],

    'cas' => [
        'url' => env('CAS_URL', ''),
        'image' => env('CAS_IMAGE', ''),
    ],

    'ginger_key' => env('GINGER_KEY', ''),

    'cookie_lifetime' => 518400,
];
