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

// All routes starting with user-{verbe}-portail-.
return [
    'description' => 'Données du portail',
    'icon' => 'gear',
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
