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

// Toutes les routes commencant par user-{verbe}-calendar-
return [
	'description' => 'Calendriers',
	'icon' => 'calendar-alt',
	'verbs' => [
		'manage' => [
			'description' => 'Gérer tout les calendriers de l\'utilisateur, de ses groupes, de ses associations/applications',
			'scopes' => [
				'users' => [
					'description' => 'Gérer les calendriers de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les calendriers que l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Gérer les calendriers que l\'utilisateur possède',
							'scopes' => [
								'client' => [
									'description' => 'Gérer les calendriers que l\'utilisateur possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Gérer les calendriers que l\'utilisateur suit',
						],
					]
				],
				'assos' => [
					'description' => 'Gérer les calendriers de chaque association',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les calendriers que chaque association a créé',
						],
						'owned' => [
							'description' => 'Gérer les calendriers que chaque association possède',
							'scopes' => [
								'client' => [
									'description' => 'Gérer les calendriers que chaque association possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Gérer les calendriers que chaque association suit',
						],
					]
				],
				'groups' => [
					'description' => 'Gérer les calendriers de chaque groupe',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les calendriers que chaque groupe a créé',
						],
						'owned' => [
							'description' => 'Gérer les calendriers que chaque groupe possède',
							'scopes' => [
								'client' => [
									'description' => 'Gérer les calendriers que chaque groupe possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Gérer les calendriers que chaque groupe suit',
						],
					]
				],
				'clients' => [
					'description' => 'Gérer les calendriers de chaque client',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les calendriers que chaque client a créé',
						],
						'owned' => [
							'description' => 'Gérer les calendriers que chaque client possède',
							'scopes' => [
								'client' => [
									'description' => 'Gérer les calendriers que chaque client possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Gérer les calendriers que chaque client suit',
						],
					]
				],
			]
		],
		'get' => [
			'description' => 'Récupérer tout les calendriers',
			'scopes' => [
				'users' => [
					'description' => 'Récupérer les calendriers de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les calendriers que l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Récupérer les calendriers que l\'utilisateur possède',
							'scopes' => [
								'client' => [
									'description' => 'Récupérer les calendriers que l\'utilisateur possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Récupérer les calendriers que l\'utilisateur suit',
						],
					]
				],
				'assos' => [
					'description' => 'Récupérer les calendriers de chaque association',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les calendriers que chaque association a créé',
						],
						'owned' => [
							'description' => 'Récupérer les calendriers que chaque association possède',
							'scopes' => [
								'client' => [
									'description' => 'Récupérer les calendriers que chaque association possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Récupérer les calendriers que chaque association suit',
						],
					]
				],
				'groups' => [
					'description' => 'Récupérer les calendriers de chaque groupe',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les calendriers que chaque groupe a créé',
						],
						'owned' => [
							'description' => 'Récupérer les calendriers que chaque groupe possède',
							'scopes' => [
								'client' => [
									'description' => 'Récupérer les calendriers que chaque groupe possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Récupérer les calendriers que chaque groupe suit',
						],
					]
				],
				'clients' => [
					'description' => 'Récupérer les calendriers de chaque client',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les calendriers que chaque client a créé',
						],
						'owned' => [
							'description' => 'Récupérer les calendriers que chaque client possède',
							'scopes' => [
								'client' => [
									'description' => 'Récupérer les calendriers que chaque client possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Récupérer les calendriers que chaque client suit',
						],
					]
				],
			]
		],
		'set' => [
			'description' => 'Modifier et créer les calendriers',
			'scopes' => [
				'users' => [
					'description' => 'Modifier et créer les calendriers de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Modifier et créer les calendriers que l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Modifier et créer les calendriers que l\'utilisateur possède',
							'scopes' => [
								'client' => [
									'description' => 'Modifier et créer les calendriers que l\'utilisateur possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Modifier et ajouter les calendriers que l\'utilisateur suit',
						],
					]
				],
				'assos' => [
					'description' => 'Modifier et créer les calendriers de chaque association',
					'scopes' => [
						'created' => [
							'description' => 'Modifier et créer les calendriers que chaque association a créé',
						],
						'owned' => [
							'description' => 'Modifier et créer les calendriers que chaque association possède',
							'scopes' => [
								'client' => [
									'description' => 'Modifier et créer les calendriers que chaque association possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Modifier et créer les calendriers que chaque association suit',
						],
					]
				],
				'groups' => [
					'description' => 'Modifier et créer les calendriers de chaque groupe',
					'scopes' => [
						'created' => [
							'description' => 'Modifier et créer les calendriers que chaque groupe a créé',
						],
						'owned' => [
							'description' => 'Modifier et créer les calendriers que chaque groupe possède',
							'scopes' => [
								'client' => [
									'description' => 'Modifier et créer les calendriers que chaque groupe possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Modifier et créer les calendriers que chaque groupe suit',
						],
					]
				],
				'clients' => [
					'description' => 'Modifier et créer les calendriers de chaque client',
					'scopes' => [
						'created' => [
							'description' => 'Modifier et créer les calendriers que chaque client a créé',
						],
						'owned' => [
							'description' => 'Modifier et créer les calendriers que chaque client possède',
							'scopes' => [
								'client' => [
									'description' => 'Modifier et créer les calendriers que chaque client possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Modifier et créer les calendriers que chaque client suit',
						],
					]
				],
			]
		],
		'create' => [
			'description' => 'Créer et faire suivre des calendriers',
			'scopes' => [
				'users' => [
					'description' => 'Créer et faire suivre des calendriers pour l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Créer des calendriers pour l\'utilisateur',
						],
						'followed' => [
							'description' => 'Faire suivre des calendriers pour l\'utilisateur',
						],
					]
				],
				'assos' => [
					'description' => 'Créer et faire suivre des calendriers pour chaque association',
					'scopes' => [
						'owned' => [
							'description' => 'Créer des calendriers pour chaque association',
						],
						'followed' => [
							'description' => 'Faire suivre des calendriers pour chaque association',
						],
					]
				],
				'groups' => [
					'description' => 'Créer et faire suivre des calendriers pour chaque groupe',
					'scopes' => [
						'owned' => [
							'description' => 'Créer des calendriers pour chaque groupe',
						],
						'followed' => [
							'description' => 'Faire suivre des calendriers pour chaque groupe',
						],
					]
				],
				'clients' => [
					'description' => 'Créer et faire suivre des calendriers pour chaque client',
					'scopes' => [
						'owned' => [
							'description' => 'Créer des calendriers pour chaque client',
						],
						'followed' => [
							'description' => 'Faire suivre des calendriers pour chaque client',
						],
					]
				],
			]
		],
	]
];
