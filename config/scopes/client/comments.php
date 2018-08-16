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

// Toutes les routes commencant par client-{verbe}-comments-
return [
    'description' => 'Commentaires',
    'icon' => 'newspaper',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer tous les commentaires',
            'scopes' => [
                'articles' => [
                    'description' => 'Gérer les commentaires sur des articles',
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer tous les commentaires',
            'scopes' => [
                'articles' => [
                    'description' => 'Récupérer les commentaires sur des articles',
                ],
            ]
        ],
        'set' => [
            'description' => 'Modifier et créer des commentaires',
            'scopes' => [
                'articles' => [
                    'description' => 'Modifier et créer des commentaires sur des articles',
                ],
            ]
        ],
        'edit' => [
            'description' => 'Modifier les commentaires',
            'scopes' => [
                'articles' => [
                    'description' => 'Modifier les commentaires sur des articles',
                ],
            ]
        ],
        'create' => [
            'description' => 'Créer des commentaires',
            'scopes' => [
                'articles' => [
                    'description' => 'Créer des commentaires sur des articles',
                ],
            ]
        ],
    ]
];