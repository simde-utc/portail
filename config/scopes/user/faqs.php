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

// All routes starting with user-{verbe}-faqs-
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
