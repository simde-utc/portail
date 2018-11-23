<?php
/**
 * Fichier de configuration des vues.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

return [
    'paths' => [
        resource_path('views'),
    ],

    'compiled' => realpath(storage_path('framework/views')),
];
