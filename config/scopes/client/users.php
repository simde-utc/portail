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

// All routes starting with client-{verbe}-users-.
return [
    'description' => 'Utilisateurs',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer totalement tous les utilisateurs',
            'scopes' => [
                'active' => [
                    'description' => 'Gérer les comptes utilisateurs actifs',
                ],
                'inactive' => [
                    'description' => 'Gérer les comptes utilisateurs non actifs',
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer la liste des utilisateurs',
            'scopes' => [
                'active' => [
                    'description' => 'Récupérer les comptes utilisateurs actifs',
                ],
                'inactive' => [
                    'description' => 'Récupérer les comptes utilisateurs non actifs',
                ],
            ]
        ],
        'set' => [
            'description' => "Gérer la création et la modification d'utilisateurs",
            'scopes' => [
                'active' => [
                    'description' => 'Créer et modifier les comptes utilisateurs actifs',
                ],
                'inactive' => [
                    'description' => 'Créer et modifier les comptes utilisateurs non actifs',
                ],
            ]
        ],
        'create' => [
            'description' => "Gérer la création d'utilisateurs",
            'scopes' => [
                'active' => [
                    'description' => 'Créer les comptes utilisateurs actifs',
                ],
                'inactive' => [
                    'description' => 'Créer les comptes utilisateurs non actifs',
                ],
            ]
        ],
        'edit' => [
            'description' => "Gérer la modification d'utilisateurs",
            'scopes' => [
                'active' => [
                    'description' => 'Modifier les comptes utilisateurs actifs',
                ],
                'inactive' => [
                    'description' => 'Modifier les comptes utilisateurs non actifs',
                ],
            ]
        ],
    ]
];
