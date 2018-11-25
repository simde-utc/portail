<?php
/**
 * Fichier de configuration des validations.
 * Helper pour les requests:
 *  validation_between('login') => donne `between:1,15`
 * Helper pour les migrations :
 *  validation_max('login') => donne `15`
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

return [
	// Login pour les assos et les étudiants.
    'login' => [
        'min' => 1,
        'max' => 15,
    ],

    'type' => [
        'min' => 1,
        'max' => 31,
    ],

    'email' => [
        'min' => 7,
        'max' => 127,
    ],

    'url' => [
        'min' => 7,
        'max' => 255,
    ],

    // Nom et prénom de personnes.
    'name' => [
        'min' => 1,
        'max' => 63,
    ],

    // Titre d'articles et d'évènements.
    'title' => [
        'min' => 1,
        'max' => 127,
    ],

    // Description courte d'assos.
    'description' => [
        'min' => 0,
        'max' => 8191,
    ],

    // Chaine de charactère courte basique.
    'string' => [
        'min' => 0,
        'max' => 255,
    ],

    // Contenu d'article.
    'article' => [
        'min' => 0,
        'max' => 16383,
    ],

    // Commentaire d'article.
    'comment' => [
        'min' => 1,
        'max' => 4095,
    ],
];
