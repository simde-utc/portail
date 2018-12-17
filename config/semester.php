<?php
/**
 * Fichier de configuration des semestres.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

return [
    'begin_at' => [
        [
            'month' => '09',
            'day' => '01',
            'time' => '00:00:00',
        ],
        [
            'month' => '02',
            'day' => '01',
            'time' => '00:00:00',
        ],
    ],

    'end_at' => [
        [
            'month' => '01',
            'day' => '31',
            'time' => '23:59:59',
        ],
        [
            'month' => '08',
            'day' => '31',
            'time' => '23:59:59',
        ],
    ],

    'name' => [
        'A',
        'P',
    ]
];
