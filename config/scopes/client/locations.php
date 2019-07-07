<?php
/**
 * List of scopes depending on routes.
 *   - Scopes definition:
 *      range + "-" + verb + "-" + category + (for each subcategory: '-' + subcategory)
 *      ex: user-get-user user-get-user-assos user-get-user-assos-collaborated
 *
 *   - Scope range definition:
 *     + user :    user_credential => nécessite que l'application soit connecté à un utilisateur
 *     + client :  client_credential => nécessite que l'application est les droits d'application indépendante d'un utilisateur
 *
 *   - Définition du verbe:
 *     + manage:  Entire ressource management.
 *       + set :  Possibility of writing/updating data.
 *         + get :  Read-only data retrievement.
 *         + create:  New data creation.
 *         + edit:    Update data.
 *       + remove:  Delete data.
 */

// All routes starting with client-{verbe}-locations-.
return [
    'description' => 'Emplacements et lieux',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer totalement les lieux et les emplacements',			'scopes' => [
                'places' => [
                    'description' => 'Gérer totalement les emplacements',
                ],
            ]
        ],
        'set' => [
            'description' => 'Modifier et créer des lieux et des emplacements',			'scopes' => [
                'places' => [
                    'description' => 'Modifier et créer des emplacements',
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer les lieux et les emplacements',			'scopes' => [
                'places' => [
                    'description' => 'Récupérer les emplacements',
                ],
            ]
        ],
        'create' => [
            'description' => 'Créer de nouveaux lieux et emplacements',			'scopes' => [
                'places' => [
                    'description' => 'Créer de nouveaux emplacements',
                ],
            ]
        ],
    ]
];
