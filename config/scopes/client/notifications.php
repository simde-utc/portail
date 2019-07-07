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

// Toutes les routes commencant par client-{verbe}-notifications-
return [
    'description' => 'Notifications',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer tous les notifications des utilisateurs',
        ],
        'get' => [
            'description' => 'Récupérer tous les notifications des utilisateurs',
        ],
        'set' => [
            'description' => 'Modifier et créer des notifications pour l\'utilisateur',
        ],
        'edit' => [
            'description' => 'Modifier les notifications des utilisateurs',
        ],
        'create' => [
            'description' => 'Créer des notifications pour l\'utilisateur',
        ],
    ]
];
