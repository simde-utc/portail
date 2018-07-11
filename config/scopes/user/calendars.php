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
	'icon' => 'calendar',
	'verbs' => [
		'manage' => [
			'description' => 'Gérer tout les calendriers',
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
								],
								'asso' => [
									'description' => 'Gérer les calendriers que l\'utilisateur possède et que mon association crée'
								],
							]
						],
						'followed' => [
							'description' => 'Gérer les calendriers que l\'utilisateur suit',
							'scopes' => [
								'users' => [
									'description' => 'Gérer les calendriers utilisateurs que l\'utilisateur suit',
								],
								'assos' => [
									'description' => 'Gérer les calendriers associatifs que l\'utilisateur suit',
								],
								'groups' => [
									'description' => 'Gérer les calendriers de groupe que l\'utilisateur suit',
								],
								'clients' => [
									'description' => 'Gérer les calendriers clients que l\'utilisateur suit',
								],
							]
						],
					]
				],
				'assos' => [
					'description' => 'Gérer les calendriers des associations de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les calendriers que chaque association de l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Gérer les calendriers que chaque association de l\'utilisateur possède',
							'scopes' => [
								'client' => [
									'description' => 'Gérer les calendriers que chaque association de l\'utilisateur possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Gérer les calendriers que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
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
								],
								'asso' => [
									'description' => 'Gérer les calendriers que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
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
								],
								'asso' => [
									'description' => 'Gérer les calendriers que chaque client de l\'utilisateur possède et que mon association crée'
								],
							]
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
								],
								'asso' => [
									'description' => 'Récupérer les calendriers que l\'utilisateur possède et que mon association crée'
								],
							]
						],
						'followed' => [
							'description' => 'Récupérer les calendriers que l\'utilisateur suit',
							'scopes' => [
								'users' => [
									'description' => 'Récupérer les calendriers utilisateurs que l\'utilisateur suit',
								],
								'assos' => [
									'description' => 'Récupérer les calendriers associatifs que l\'utilisateur suit',
								],
								'groups' => [
									'description' => 'Récupérer les calendriers de groupe que l\'utilisateur suit',
								],
								'clients' => [
									'description' => 'Récupérer les calendriers clients que l\'utilisateur suit',
								],
							]
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
								],
								'asso' => [
									'description' => 'Récupérer les calendriers que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
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
								],
								'asso' => [
									'description' => 'Récupérer les calendriers que chaque groupe de l\'utilisateur possède et que mon association crée'
								],
							]
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
								],
								'asso' => [
									'description' => 'Récupérer les calendriers que chaque client de l\'utilisateur possède et que mon association crée'
								],
							]
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
								],
								'asso' => [
									'description' => 'Modifier et créer les calendriers que l\'utilisateur possède et que mon association crée'
								],
							]
						],
						'followed' => [
							'description' => 'Modifier et créer les calendriers que l\'utilisateur suit',
							'scopes' => [
								'users' => [
									'description' => 'Modifier et créer les calendriers utilisateurs que l\'utilisateur suit',
								],
								'assos' => [
									'description' => 'Modifier et créer les calendriers associatifs que l\'utilisateur suit',
								],
								'groups' => [
									'description' => 'Modifier et créer les calendriers de groupe que l\'utilisateur suit',
								],
								'clients' => [
									'description' => 'Modifier et créer les calendriers clients que l\'utilisateur suit',
								],
							]
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
								],
								'asso' => [
									'description' => 'Modifier et créer les calendriers que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
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
								],
								'asso' => [
									'description' => 'Modifier et créer les calendriers que chaque groupe de l\'utilisateur possède et que mon association crée'
								],
							]
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
								],
								'asso' => [
									'description' => 'Modifier et créer les calendriers que chaque client de l\'utilisateur possède et que mon association crée'
								],
							]
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
						'created' => [
							'description' => 'Créer des calendriers au nom de l\'utilisateur',
						],
						'owned' => [
							'description' => 'Créer des calendriers pour l\'utilisateur',
							'scopes' => [
								'client' => [
									'description' => 'Créer des calendriers pour l\'utilisateur au noom de mon application'
								],
								'asso' => [
									'description' => 'Créer les calendriers pour l\'utilisateur possède et que mon association crée'
								],
							]
						],
						'followed' => [
							'description' => 'Faire suivre des calendriers à l\'utilisateur',
							'scopes' => [
								'users' => [
									'description' => 'Faire suivre des calendriers utilisateurs à l\'utilisateur',
								],
								'assos' => [
									'description' => 'Faire suivre des calendriers associatifs à l\'utilisateur',
								],
								'groups' => [
									'description' => 'Faire suivre des calendriers de groupe à l\'utilisateur',
								],
								'clients' => [
									'description' => 'Faire suivre des calendriers de client à l\'utilisateur',
								],
							]
						],
					]
				],
				'assos' => [
					'description' => 'Créer des calendriers pour chaque association',
					'scopes' => [
						'created' => [
							'description' => 'Créer des calendriers au nom d\'une association de l\'utilisateur',
						],
						'owned' => [
							'description' => 'Créer des calendriers pour une association de l\'utilisateur',
							'scopes' => [
								'client' => [
									'description' => 'Créer des calendriers pour des associations de l\'utilisateur au noom de mon application'
								],
								'asso' => [
									'description' => 'Créer les calendriers pour chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'groups' => [
					'description' => 'Créer des calendriers pour chaque groupe',
					'scopes' => [
						'created' => [
							'description' => 'Créer des calendriers au nom d\'un groupe de l\'utilisateur',
						],
						'owned' => [
							'description' => 'Créer des calendriers pour un groupe de l\'utilisateur',
							'scopes' => [
								'client' => [
									'description' => 'Créer des calendriers pour des groupes de l\'utilisateur au noom de mon application'
								],
								'asso' => [
									'description' => 'Créer les calendriers pour chaque groupe de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'clients' => [
					'description' => 'Créer des calendriers pour chaque client',
					'scopes' => [
						'created' => [
							'description' => 'Créer des calendriers au nom d\'un client de l\'utilisateur',
						],
						'owned' => [
							'description' => 'Créer des calendriers pour un client de l\'utilisateur',
							'scopes' => [
								'client' => [
									'description' => 'Créer des calendriers pour des applications de l\'utilisateur au noom de mon application'
								],
								'asso' => [
									'description' => 'Créer les calendriers pour chaque application de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
			]
		],
	]
];
