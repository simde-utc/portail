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

// All routes starting with client-{verbe}-groups-.
return [
    'description' => 'Groupes',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer les groupes des utilisateursr',
            'scopes' => [
                'enabled' => [
                    'description' => 'Gérer les groupes actifs des utilisateursr',
                ],
                'disabled' => [
                    'description' => 'Gérer les groupes inactifs des utilisateursr',
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer les groupes des utilisateursr',
            'scopes' => [
                'enabled' => [
                    'description' => 'Récupérer les groupes actifs des utilisateursr',
                ],
                'disabled' => [
                    'description' => 'Récupérer les groupes inactifs des utilisateursr',
                ],
            ]
        ],
        'set' => [
            'description' => 'Modifier les groupes des utilisateursr',
            'scopes' => [
                'enabled' => [
                    'description' => 'Modifier les groupes actifs des utilisateursr',
                ],
                'disabled' => [
                    'description' => 'Modifier les groupes inactifs des utilisateursr',
                ],
            ]
        ],
    ]
];
