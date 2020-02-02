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
    'asso' => [
        'amount' => 2,
        'type' => [
            'identifiers' => '1901,commission,club,projet',
            'descriptions' => 'Association loi 1901,Commission,Club,Projet',
        ],
        'parents' => 'bde,poleae,polesec,polete,polevdc',
    ],
    'partner' => [
        'amount' => 0,
        'description_length' => 500,
        'image_width' => 300,
        'image_height' => 400,
    ],
];
