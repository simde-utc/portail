<?php
/**
 * Validation configuration file.
 * Helper for requests:
 *  validation_between('login') => returns `between:1,15`.
 * Helper for migrations :
 *  validation_max('login') => returns `15`.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

return [
	// Login for associations and students.
    'login' => [
        'min' => 1,
        'max' => 31,
    ],

    'type' => [
        'min' => 1,
        'max' => 31,
    ],

    'email' => [
        'min' => 7,
        'max' => 255,
    ],

    'url' => [
        'min' => 7,
        'max' => 255,
    ],

    // First and last name.
    'name' => [
        'min' => 1,
        'max' => 127,
    ],

    // Event and article title.
    'title' => [
        'min' => 1,
        'max' => 255,
    ],

    // Short association description.
    'short_description' => [
        'min' => 0,
        'max' => 255,
    ],

    // Association description.
    'description' => [
        'min' => 0,
        'max' => 8191,
    ],

    // Short basic string.
    'string' => [
        'min' => 0,
        'max' => 255,
    ],

    // Article content.
    'article' => [
        'min' => 0,
        'max' => 16383,
    ],

    // Article comment.
    'comment' => [
        'min' => 1,
        'max' => 4095,
    ],
];
