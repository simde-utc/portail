<?php

/*
 * Liste des scopes en fonction des routes
 *   - Définition des scopes:
 *      portée + "-" + verbe + "-" + categorie + (pour chaque sous-catégorie: '-' + sous-catégorie)
 *      ex: user-get-user user-get-user-assos user-get-user-assos-collaborated
 *
 *   - Définition de la portée des scopes:
 *     + user :    user_credential => nécessite que l'application soit connecté à un utilisateur
 *     + client :  client_credential => nécessite que l'application est les droits d'application indépendante d'un utilisateur
 *
 *   - Définition du verbe:
 *     + manage:  gestion de la ressource entière
 *       + set :  posibilité d'écrire et modifier les données
 *         + get :  récupération des informations en lecture seule
 *         + create:  créer une donnée associée
 *         + edit:    modifier une donnée
 *       + remove:  supprimer une donnée
 */

// Toutes les routes commencant par user-{verbe}-notifications-
return [
    'description' => 'Notifications',
    'icon' => 'bell',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer tous les notifications de l\'utilisateur',
        ],
        'get' => [
            'description' => 'Récupérer tous les notifications de l\'utilisateur',
        ],
        'set' => [
            'description' => 'Modifier et créer des notifications pour l\'utilisateur',
        ],
        'edit' => [
            'description' => 'Modifier les notifications de l\'utilisateur',
        ],
        'create' => [
            'description' => 'Créer des notifications pour l\'utilisateur',
        ],
    ]
];
