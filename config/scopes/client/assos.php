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

// Toutes les routes commencant par client-{verbe}-assos-
return [
	'description' => 'Permet à l\'application de posséder différentes actions sur toutes les associations',
	'verbs' => [
		'manage' => [
			'description' => 'Gérer totalement toutes les associations',
			'scopes' => [
				'followers' => [
					'description' => 'Gérer totalement toutes les personnes suivant des associations',
					'scopes' => [
						'now' => [
							'description' => 'Gérer totalement toutes les personnes suivant des associations de l\'actuel semestre',
						],
					]
				],
				'members' => [
					'description' => 'Gérer totalement tous les members des associations',
					'scopes' => [
						'now' => [
							'description' => 'Gérer totalement tous les membres des associations de l\'actuel semestre',
						],
					]
				],
			]
		],
		'get' => [
			'description' => 'Récupérer la liste des utilisateurs',
			'scopes' => [
				'followers' => [
					'description' => 'Récupérer la liste des personnes suivant des associations',
					'scopes' => [
						'now' => [
							'description' => 'Récupérer la liste des personnes suivant des associations de l\'actuel semestre',
						]
					]
				],
				'members' => [
					'description' => 'Récupérer la liste des members des associations',
					'scopes' => [
						'now' => [
							'description' => 'Récupérer la liste des members des associations de l\'actuel semestre',
						]
					]
				],
			]
		],
		'set' => [
			'description' => "Gérer la création et la modification d'utilisateurs",
			'scopes' => [
				'followers' => [
					'description' => 'Gérer la création et la modification des personnes suivant des associations',
					'scopes' => [
						'now' => [
							'description' => 'Gérer la création et la modification des personnes suivant des associations de l\'actuel semestre',
						]
					]
				],
				'members' => [
					'description' => 'Gérer la création et la modification des members des associations',
					'scopes' => [
						'now' => [
							'description' => 'Gérer la création et la modification des members des associations de l\'actuel semestre',
						]
					]
				],
			]
		],
	]
];
