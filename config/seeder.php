<?php
/**
 * Seeders configuration file.
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

return [
    'article' => [
        'amount' => 20,
        'generate_images' => false,
        'image_width' => 300,
        'image_height' => 400,
    ],
    'asso' => [
        'amount' => 40,
        'type' => [
            'identifiers' => '1901,commission,club,projet',
            'descriptions' => 'Association loi 1901,Commission,Club,Projet',
        ],
        'parents' => 'bde,poleae,polesec,polete,polevdc',
        'generate_images' => false,
        'image_width' => 300,
        'image_height' => 400,
    ],
    // We recommand seeding 1000 memberships if multiple_semester is true
    'membership' => [
        'amount' => 400,
        // Enables membership to be created for past semesters (useful for carrers)
        'multiple_semesters' => false,
    ],
    'partner' => [
        'amount' => 12,
        'description_length' => 500,
        'generate_images' => false,
        'image_width' => 300,
        'image_height' => 400,
    ],
    'user' => [
        'amount' => 50,
        'generate_images' => false,
        'image_width' => 300,
        'image_height' => 400,
    ],
];
