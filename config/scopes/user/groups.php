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

// Toutes les routes commencant par user-{verbe}-groups-
return [
    'description' => 'Groupes',
    'icon' => 'users',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer les groupes de l\'utilisateur',
            'scopes' => [
                'enabled' => [
                    'description' => 'Gérer les groupes actifs de l\'utilisateur',
                ],
                'disabled' => [
                    'description' => 'Gérer les groupes inactifs de l\'utilisateur',
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer les groupes de l\'utilisateur',
            'scopes' => [
                'enabled' => [
                    'description' => 'Récupérer les groupes actifs de l\'utilisateur',
                ],
                'disabled' => [
                    'description' => 'Récupérer les groupes inactifs de l\'utilisateur',
                ],
            ]
        ],
        'set' => [
            'description' => 'Modifier les groupes de l\'utilisateur',
            'scopes' => [
                'enabled' => [
                    'description' => 'Modifier les groupes actifs de l\'utilisateur',
                ],
                'disabled' => [
                    'description' => 'Modifier les groupes inactifs de l\'utilisateur',
                ],
            ]
        ],
    ]
];
