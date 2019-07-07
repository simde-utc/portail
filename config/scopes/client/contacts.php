<?php
/**
 * List of scopes depending on routes
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
 *         + get :  Read-only data retrievement
 *         + create:  New data creation
 *         + edit:    update data
 *       + remove:  delete data
 */

// Toutes les routes commencant par client-{verbe}-contacts-
return [
    'description' => 'Contacts',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer les moyens et les informations de contact des utilisateur, des associations et des groupes',
            'scopes' => [
                'users' => [
                    'description' => 'Gérer les moyens et les informations de contact de chaque utlisateur',
                ],
                'assos' => [
                    'description' => 'Gérer les moyens et les informations de contact de chaque association',
                ],
                'groups' => [
                    'description' => 'Gérer les moyens et les informations de contact de chaque groupe',
                ],
            ]
        ],
        'set' => [
            'description' => 'Modifier et ajouter des moyens des informations de contact des utilisateur, des associations et des groupes',
            'scopes' => [
                'users' => [
                    'description' => 'Modifier et ajouter des moyens des informations de contact de chaque utlisateur',
                ],
                'assos' => [
                    'description' => 'Modifier et ajouter des moyens des informations de contact de chaque association',
                ],
                'groups' => [
                    'description' => 'Modifier et ajouter des moyens des informations de contact de chaque groupe',
                ],
            ]
        ],
        'create' => [
            'description' => 'Ajouter des moyens des informations de contact des utilisateur, des associations et des groupes',
            'scopes' => [
                'users' => [
                    'description' => 'Ajouter des moyens des informations de contact de chaque utlisateur',
                ],
                'assos' => [
                    'description' => 'Ajouter des moyens des informations de contact de chaque association',
                ],
                'groups' => [
                    'description' => 'Ajouter des moyens des informations de contact de chaque groupe',
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer les moyens et les informations de contact des utilisateur, des associations et des groupes',
            'scopes' => [
                'users' => [
                    'description' => 'Récupérer les moyens et les informations de contact de chaque utlisateur',
                ],
                'assos' => [
                    'description' => 'Récupérer les moyens et les informations de contact de chaque association',
                ],
                'groups' => [
                    'description' => 'Récupérer les moyens et les informations de contact de chaque groupe',
                ],
            ]
        ],
    ]
];
