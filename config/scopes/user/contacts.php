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

// All routes starting with user-{verbe}-contacts-.
return [
    'description' => 'Contacts',
    'icon' => 'address-card',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer les moyens et les informations de contact de l\'utilisateur, de ses associations et de ses groupes',
            'scopes' => [
                'users' => [
                    'description' => 'Gérer les moyens et les informations de contact de l\'utilisateur',
                ],
                'assos' => [
                    'description' => 'Gérer les moyens et les informations de contact des associations de l\'utilisateur',
                ],
                'groups' => [
                    'description' => 'Gérer les moyens et les informations de contact des groupes de l\'utilisateur',
                ],
            ]
        ],
        'set' => [
            'description' => 'Modifier et ajouter des moyens et des informations de contact de l\'utilisateur, de ses associations et de ses groupes',
            'scopes' => [
                'users' => [
                    'description' => 'Modifier et ajouter des moyens et des informations de contact de l\'utilisateur',
                ],
                'assos' => [
                    'description' => 'Modifier et ajouter des moyens et des informations de contact des associations de l\'utilisateur',
                ],
                'groups' => [
                    'description' => 'Modifier et ajouter des moyens et des informations de contact des groupes de l\'utilisateur',
                ],
            ]
        ],
        'create' => [
            'description' => 'Ajouter des moyens et des informations de contact de l\'utilisateur, de ses associations et de ses groupes',
            'scopes' => [
                'users' => [
                    'description' => 'Ajouter des moyens et des informations de contact de l\'utilisateur',
                ],
                'assos' => [
                    'description' => 'Ajouter des moyens et des informations de contact des associations de l\'utilisateur',
                ],
                'groups' => [
                    'description' => 'Ajouter des moyens et des informations de contact des groupes de l\'utilisateur',
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer les moyens et les informations de contact de l\'utilisateur, de ses associations et de ses groupes',
            'scopes' => [
                'users' => [
                    'description' => 'Récupérer les moyens et les informations de contact de l\'utilisateur',
                ],
                'assos' => [
                    'description' => 'Récupérer les moyens et les informations de contact des associations de l\'utilisateur',
                ],
                'groups' => [
                    'description' => 'Récupérer les moyens et les informations de contact des groupes de l\'utilisateur',
                ],
            ]
        ],
    ]
];
