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

// All routes starting with client-{verbe}-portail-
return [
    'description' => 'Données du portail',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer toutes les données du Portail',
            'scopes' => [
                'visibility' => [
                    'description' => 'Gérer les visibilités',
                ],
            ]
        ],
    ]
];
