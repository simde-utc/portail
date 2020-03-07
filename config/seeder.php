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
        'amount' => 20,
        'type' => [
            'identifiers' => '1901,commission,club,projet',
            'descriptions' => 'Association loi 1901,Commission,Club,Projet',
        ],
        'parents' => 'bde,poleae,polesec,polete,polevdc',
        'generate_images' => false,
        'image_width' => 300,
        'image_height' => 400,
    ],
    'partner' => [
        'amount' => 12,
        'description_length' => 500,
        'generate_images' => false,
        'image_width' => 300,
        'image_height' => 400,
    ],
    'user' => [
        'amount' => 10,
        'generate_images' => false,
        'image_width' => 300,
        'image_height' => 400,
        // Memberships
        // We recommand seeding 1000 memberships if multiple_semester is true
        'membership' => [
            'amount' => 50,
            // Enables membership to be created for past semesters (useful for carrers)
            'multiple_semesters' => false,
        ],
        // Pending memberships
        'joining_assos' => [
            'admin' => [
                'amount' => 2,
            ],
            'amount' => 0,
        ],
        // Followed Associations
        'followed_assos' => [
            // Amount of data for the admin user (in dev mode it's probably you)
            'admin' => [
                'amount' => 2,
            ],
            // Amount of total followed assos for ohter users
            'amount' => 0,
        ],
        // Followed Services
        'followed_services' => [
            // Amount of data for the admin user (in dev mode it's probably you)
            'admin' => [
                'amount' => 3,
            ],
            // Amount of total followed services for ohter users
            'amount' => 0,
        ],
    ],
];
