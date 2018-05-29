<?php

/*
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

// Toutes les routes commencant par client-{verbe}-groups-
return [
    'description' => 'Groupes',
    'verbs' => [
        'get' => [
            'description' => 'Récupérer la liste de tous les groupes',
            'scopes' => [
                'members' => [
                    'description' => 'Récupérer la liste des membres des groupes',
                ],
				'enabled' => [
					'description' => 'Récupérer la liste des groupes actifs',
					'scopes' => [
		                'members' => [
		                    'description' => 'Récupérer la liste des membres des groupes actifs',
		                ],
					]
				],
				'disabled' => [
					'description' => 'Récupérer la liste des groupes inactifs',
					'scopes' => [
		                'members' => [
		                    'description' => 'Récupérer la liste des membres des groupes inactifs',
		                ],
					]
				],
            ]
        ],
    ]
];
