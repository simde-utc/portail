<?php

/*
 * Liste des scopes en fonction des routes
 *   - Définition des scopes:
 *   	portée + "-" + verbe + "-" + categorie + (pour chaque sous-catégorie: '-' + sous-catégorie)
 *   	ex: user-get-user user-get-user-assos user-get-user-assos-followed
 *
 *   - Définition de la portée des scopes:
 *     + user :    user_credential => nécessite que l'application soit connecté à un utilisateur
 *     + client :  client_credential => nécessite que l'application est les droits d'application indépendante d'un utilisateur
 *
 *   - Définition du verbe:
 *     + manage:  gestion de la ressource entière
 *       + get :  récupération des informations en lecture seule
 *       + set :  posibilité d'écrire et modifier les données
 *         + create:  créer une donnée associée
 *         + edit:    modifier une donnée
 *         + remove:  supprimer une donnée
 */

// Toutes les routes commencant par user-{verbe}-assos-
return [
	'description' => 'Permet à l\'application de posséder différentes actions sur tous les utilisateurs',
	'verbs' => [
		'manage' => [
			'description' => 'Gérer les associations suivies et faites par l\'utilisateur',
			'scopes' => [
				'done' => [
					'description' => 'Gérer les associations faites par l\'utilisateur',
					'scopes' => [
						'now' => [
							'description' => 'Gérer les associations faites par l\'utilisateur durant l\'actuel semestre',
						],
					]
				],
				'followed' => [
					'description' => 'Gérer les associations suivies par l\'utilisateur',
					'scopes' => [
						'now' => [
							'description' => 'Gérer les associations suivies par l\'utilisateur durant l\'actuel semestre',
						],
					]
				],
			]
		],
		'get' => [
			'description' => 'Récupérer toutes les associations suivies et faites par l\'utilisateur',
			'scopes' => [
				'done' => [
					'description' => 'Récupérer toutes les associations faites par l\'utilisateur',
					'scopes' => [
						'now' => [
							'description' => 'Récupérer toutes les associations faites par l\'utilisateur durant l\'actuel semestre',
						],
					]
				],
				'followed' => [
					'description' => 'Récupérer toutes les associations suivies par l\'utilisateur',
					'scopes' => [
						'now' => [
							'description' => 'Récupérer toutes les associations suivies par l\'utilisateur durant l\'actuel semestre',
						],
					]
				],
				'members' => [
					'description' => 'Récupérer tous les membres des associations suivies et faites par l\'utilisateur',
					'scopes' => [
						'now' => [
							'description' => 'Récupérer tous les membres des associations suivies et faites par l\'utilisateur durant l\'actuel semestre',
						],
					]
				],
			]
		],
		'set' => [
			'description' => 'Créer et modifier les associations suivies et faites par l\'utilisateur',
			'scopes' => [
				'done' => [
					'description' => 'Créer et modifier les associations faites par l\'utilisateur',
					'scopes' => [
						'now' => [
							'description' => 'Créer et modifier les associations faites par l\'utilisateur durant l\'actuel semestre',
						],
					]
				],
				'followed' => [
					'description' => 'Créer et modifier les associations suivies par l\'utilisateur',
					'scopes' => [
						'now' => [
							'description' => 'Créer et modifier les associations suivies par l\'utilisateur durant l\'actuel semestre',
						],
					]
				],
			]
		],
	]
];
