<?php
/**
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

// Toutes les routes commencant par user-{verbe}-faqs-
return [
    'description' => 'FAQs',
    'icon' => 'question',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer toutes les FAQs',
            'scopes' => [
                'categories' => [
                    'description' => 'Gérer toutes les catégories FAQs',
                ],
                'questions' => [
                    'description' => 'Gérer toutes les questions FAQs',
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer toutes les FAQs',
            'scopes' => [
                'categories' => [
                    'description' => 'Récupérer toutes les catégories FAQs',
                ],
                'questions' => [
                    'description' => 'Récupérer toutes les questions FAQs',
                ],
            ]
        ],
        'set' => [
            'description' => 'Créer et modifier des FAQs',
            'scopes' => [
                'categories' => [
                    'description' => 'Créer et modifier des catégories FAQs',
                ],
                'questions' => [
                    'description' => 'Créer et modifier des questions FAQs',
                ],
            ]
        ],
        'edit' => [
            'description' => 'Modifier des FAQs',
            'scopes' => [
                'categories' => [
                    'description' => 'Modifier des catégories FAQs',
                ],
                'questions' => [
                    'description' => 'Modifier des questions FAQs',
                ],
            ]
        ],
        'create' => [
            'description' => 'Créer des FAQs',
            'scopes' => [
                'categories' => [
                    'description' => 'Créer des catégories FAQs',
                ],
                'questions' => [
                    'description' => 'Créer des questions FAQs',
                ],
            ]
        ],
        'remove' => [
            'description' => 'Créer toutes les FAQs',
            'scopes' => [
                'categories' => [
                    'description' => 'Créer toutes les catégories FAQs',
                ],
                'questions' => [
                    'description' => 'Créer toutes les questions FAQs',
                ],
            ]
        ],
    ],
];
