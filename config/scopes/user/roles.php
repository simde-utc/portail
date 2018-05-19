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

// Toutes les routes commencant par user-{verbe}-roles-
return [
    'description' => 'Roles',
    'icon' => 'gavel',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer tous les types de rôles et les rôles de l\'utilisateur (rôles systèmes, rôles associations, rôles groupes)',
			'scopes' => [
                'types' => [
                    'description' => 'Gérer toutes les types de rôles (rôles systèmes, rôles associations, rôles groupes)',
                    'scopes' => [
                        'users' => [
        					'description' => 'Gérer toutes les types de rôles au sein du système',
                        ],
        				'assos' => [
        					'description' => 'Gérer toutes les types de rôles au sein des associations',
        				],
        				'groups' => [
        					'description' => 'Gérer toutes les types de rôles au sein des groupes',
        				],
                    ]
                ],
				'users' => [
					'description' => 'Gérer les rôles de l\'utilisateur au sein du système',
                ],
				'assos' => [
					'description' => 'Gérer les rôles de l\'utilisateur au sein des associations',
				],
				'groups' => [
					'description' => 'Gérer les rôles de l\'utilisateur au sein des groupes',
				],
			]
        ],
	    'get' => [
            'description' => 'Récupérer tous les types de rôles et les rôles de l\'utilisateur (rôles systèmes, rôles associations, rôles groupes)',
			'scopes' => [
                'types' => [
                    'description' => 'Récupérer toutes les types de rôles (rôles systèmes, rôles associations, rôles groupes)',
                    'scopes' => [
                        'users' => [
        					'description' => 'Récupérer toutes les types de rôles au sein du système',
                        ],
        				'assos' => [
        					'description' => 'Récupérer toutes les types de rôles au sein des associations',
        				],
        				'groups' => [
        					'description' => 'Récupérer toutes les types de rôles au sein des groupes',
        				],
                    ]
                ],
				'users' => [
					'description' => 'Récupérer les rôles de l\'utilisateur au sein du système',
                ],
				'assos' => [
					'description' => 'Récupérer les rôles de l\'utilisateur au sein des associations',
				],
				'groups' => [
					'description' => 'Récupérer les rôles de l\'utilisateur au sein des groupes',
				],
			]
	    ],
	    'set' => [
            'description' => 'Modifier tous les types de rôles et les rôles de l\'utilisateur (rôles systèmes, rôles associations, rôles groupes)',
			'scopes' => [
                'types' => [
                    'description' => 'Modifier toutes les types de rôles (rôles systèmes, rôles associations, rôles groupes)',
                    'scopes' => [
                        'users' => [
        					'description' => 'Modifier toutes les types de rôles au sein du système',
                        ],
        				'assos' => [
        					'description' => 'Modifier toutes les types de rôles au sein des associations',
        				],
        				'groups' => [
        					'description' => 'Modifier toutes les types de rôles au sein des groupes',
        				],
                    ]
                ],
				'users' => [
					'description' => 'Modifier les rôles de l\'utilisateur au sein du système',
                ],
				'assos' => [
					'description' => 'Modifier les rôles de l\'utilisateur au sein des associations',
				],
				'groups' => [
					'description' => 'Modifier les rôles de l\'utilisateur au sein des groupes',
				],
			]
	    ],
    ]
];
