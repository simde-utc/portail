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

// All routes starting with user-{verbe}-services-
return [
    'description' => 'Services',
    'icon' => 'server',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer tous les services créés et suivis par l\'utilisateur',
            'scopes' => [
                'created' => [
                    'description' => 'Gérer tous les services créés',
                ],
                'followed' => [
                    'description' => 'Gérer tous les services suivis par l\'utilisateur',
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer tous les services',
            'scopes' => [
                'created' => [
                    'description' => 'Récupérer tous les services créés',
                ],
                'followed' => [
                    'description' => 'Récupérer tous les services suivis par l\'utilisateur',
                ],
            ]
        ],
        'set' => [
            'description' => 'Modifier et créer des services',
            'scopes' => [
                'created' => [
                    'description' => 'Modifier et créer tous les services créés',
                ],
                'followed' => [
                    'description' => 'Modifier et créer tous les services suivis par l\'utilisateur',
                ],
            ]
        ],
        'edit' => [
            'description' => 'Modifier les services',
            'scopes' => [
                'created' => [
                    'description' => 'Modifier tous les services créés',
                ],
                'followed' => [
                    'description' => 'Modifier tous les services suivis par l\'utilisateur',
                ],
            ]
        ],
        'create' => [
            'description' => 'Créer des services',
            'scopes' => [
                'created' => [
                    'description' => 'Créer tous les services créés',
                ],
                'followed' => [
                    'description' => 'Créer tous les services suivis par l\'utilisateur',
                ],
            ]
        ],
    ]
];
