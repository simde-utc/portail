<?php
/**
 * Liste des scopes en fonction des routes
 *   - Définition des scopes:
 *      portée + "-" + verbe + "-" + categorie + (pour chaque sous-catégorie: '-' + sous-catégorie)
 *      ex: user-get-user user-get-user-assos user-get-user-assos-followed
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

// Toutes les routes commencant par user-{verbe}-locations-
return [
    'description' => 'Emplacements et lieux',
    'icon' => 'map-marker',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer totalement les lieux et les emplacements',			'scopes' => [
                'places' => [
                    'description' => 'Gérer totalement les emplacements',
                ],
            ]
        ],
        'set' => [
            'description' => 'Modifier et créer des lieux et des emplacements',			'scopes' => [
                'places' => [
                    'description' => 'Modifier et créer des emplacements',
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer les lieux et les emplacements',			'scopes' => [
                'places' => [
                    'description' => 'Récupérer les emplacements',
                ],
            ]
        ],
        'create' => [
            'description' => 'Créer de nouveaux lieux et emplacements',			'scopes' => [
                'places' => [
                    'description' => 'Créer de nouveaux emplacements',
                ],
            ]
        ],
    ]
];
