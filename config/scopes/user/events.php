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

// Toutes les routes commencant par user-{verbe}-events-
return [
	'description' => 'Evènements',
	'icon' => 'calendar-o',
	'verbs' => [
		'manage' => [
			'description' => 'Gérer tout les évènements',
			'scopes' => [
				'users' => [
					'description' => 'Gérer les évènements de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les évènements que l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Gérer les évènements que l\'utilisateur possède',
							'scopes' => [
								'client' => [
									'description' => 'Gérer les évènements que l\'utilisateur possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Gérer les évènements que l\'utilisateur possède et que mon association crée'
								],
							]
						],
						'followed' => [
							'description' => 'Gérer les évènements que l\'utilisateur suit',
							'scopes' => [
								'users' => [
									'description' => 'Gérer les évènements utilisateurs que l\'utilisateur suit',
								],
								'assos' => [
									'description' => 'Gérer les évènements associatifs que l\'utilisateur suit',
								],
								'groups' => [
									'description' => 'Gérer les évènements de groupe que l\'utilisateur suit',
								],
								'clients' => [
									'description' => 'Gérer les évènements clients que l\'utilisateur suit',
								],
							]
						],
					]
				],
				'assos' => [
					'description' => 'Gérer les évènements des associations de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les évènements que chaque association de l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Gérer les évènements que chaque association de l\'utilisateur possède',
							'scopes' => [
								'client' => [
									'description' => 'Gérer les évènements que chaque association de l\'utilisateur possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Gérer les évènements que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'groups' => [
					'description' => 'Gérer les évènements de chaque groupe',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les évènements que chaque groupe a créé',
						],
						'owned' => [
							'description' => 'Gérer les évènements que chaque groupe possède',
							'scopes' => [
								'client' => [
									'description' => 'Gérer les évènements que chaque groupe possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Gérer les évènements que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'clients' => [
					'description' => 'Gérer les évènements de chaque client',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les évènements que chaque client a créé',
						],
						'owned' => [
							'description' => 'Gérer les évènements que chaque client possède',
							'scopes' => [
								'client' => [
									'description' => 'Gérer les évènements que chaque client possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Gérer les évènements que chaque client de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
			]
		],
		'get' => [
			'description' => 'Récupérer tout les évènements',
			'scopes' => [
				'users' => [
					'description' => 'Récupérer les évènements de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les évènements que l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Récupérer les évènements que l\'utilisateur possède',
							'scopes' => [
								'client' => [
									'description' => 'Récupérer les évènements que l\'utilisateur possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Récupérer les évènements que l\'utilisateur possède et que mon association crée'
								],
							]
						],
						'followed' => [
							'description' => 'Récupérer les évènements que l\'utilisateur suit',
							'scopes' => [
								'users' => [
									'description' => 'Récupérer les évènements utilisateurs que l\'utilisateur suit',
								],
								'assos' => [
									'description' => 'Récupérer les évènements associatifs que l\'utilisateur suit',
								],
								'groups' => [
									'description' => 'Récupérer les évènements de groupe que l\'utilisateur suit',
								],
								'clients' => [
									'description' => 'Récupérer les évènements clients que l\'utilisateur suit',
								],
							]
						],
					]
				],
				'assos' => [
					'description' => 'Récupérer les évènements de chaque association',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les évènements que chaque association a créé',
						],
						'owned' => [
							'description' => 'Récupérer les évènements que chaque association possède',
							'scopes' => [
								'client' => [
									'description' => 'Récupérer les évènements que chaque association possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Récupérer les évènements que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'groups' => [
					'description' => 'Récupérer les évènements de chaque groupe',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les évènements que chaque groupe a créé',
						],
						'owned' => [
							'description' => 'Récupérer les évènements que chaque groupe possède',
							'scopes' => [
								'client' => [
									'description' => 'Récupérer les évènements que chaque groupe possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Récupérer les évènements que chaque groupe de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'clients' => [
					'description' => 'Récupérer les évènements de chaque client',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les évènements que chaque client a créé',
						],
						'owned' => [
							'description' => 'Récupérer les évènements que chaque client possède',
							'scopes' => [
								'client' => [
									'description' => 'Récupérer les évènements que chaque client possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Récupérer les évènements que chaque client de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
			]
		],
		'set' => [
			'description' => 'Modifier et créer les évènements',
			'scopes' => [
				'users' => [
					'description' => 'Modifier et créer les évènements de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Modifier et créer les évènements que l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Modifier et créer les évènements que l\'utilisateur possède',
							'scopes' => [
								'client' => [
									'description' => 'Modifier et créer les évènements que l\'utilisateur possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Modifier et créer les évènements que l\'utilisateur possède et que mon association crée'
								],
							]
						],
						'followed' => [
							'description' => 'Modifier et créer les évènements que l\'utilisateur suit',
							'scopes' => [
								'users' => [
									'description' => 'Modifier et créer les évènements utilisateurs que l\'utilisateur suit',
								],
								'assos' => [
									'description' => 'Modifier et créer les évènements associatifs que l\'utilisateur suit',
								],
								'groups' => [
									'description' => 'Modifier et créer les évènements de groupe que l\'utilisateur suit',
								],
								'clients' => [
									'description' => 'Modifier et créer les évènements clients que l\'utilisateur suit',
								],
							]
						],
					]
				],
				'assos' => [
					'description' => 'Modifier et créer les évènements de chaque association',
					'scopes' => [
						'created' => [
							'description' => 'Modifier et créer les évènements que chaque association a créé',
						],
						'owned' => [
							'description' => 'Modifier et créer les évènements que chaque association possède',
							'scopes' => [
								'client' => [
									'description' => 'Modifier et créer les évènements que chaque association possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Modifier et créer les évènements que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'groups' => [
					'description' => 'Modifier et créer les évènements de chaque groupe',
					'scopes' => [
						'created' => [
							'description' => 'Modifier et créer les évènements que chaque groupe a créé',
						],
						'owned' => [
							'description' => 'Modifier et créer les évènements que chaque groupe possède',
							'scopes' => [
								'client' => [
									'description' => 'Modifier et créer les évènements que chaque groupe possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Modifier et créer les évènements que chaque groupe de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'clients' => [
					'description' => 'Modifier et créer les évènements de chaque client',
					'scopes' => [
						'created' => [
							'description' => 'Modifier et créer les évènements que chaque client a créé',
						],
						'owned' => [
							'description' => 'Modifier et créer les évènements que chaque client possède',
							'scopes' => [
								'client' => [
									'description' => 'Modifier et créer les évènements que chaque client possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Modifier et créer les évènements que chaque client de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
			]
		],
		'create' => [
			'description' => 'Créer et faire suivre des évènements',
			'scopes' => [
				'users' => [
					'description' => 'Créer et faire suivre des évènements pour l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Créer des évènements au nom de l\'utilisateur',
						],
						'owned' => [
							'description' => 'Créer des évènements pour l\'utilisateur',
							'scopes' => [
								'client' => [
									'description' => 'Créer des évènements pour l\'utilisateur au nom de mon application'
								],
								'asso' => [
									'description' => 'Créer les évènements pour l\'utilisateur possède et que mon association crée'
								],
							]
						],
						'followed' => [
							'description' => 'Faire suivre des évènements à l\'utilisateur',
							'scopes' => [
								'users' => [
									'description' => 'Faire suivre des évènements utilisateurs à l\'utilisateur',
								],
								'assos' => [
									'description' => 'Faire suivre des évènements associatifs à l\'utilisateur',
								],
								'groups' => [
									'description' => 'Faire suivre des évènements de groupe à l\'utilisateur',
								],
								'clients' => [
									'description' => 'Faire suivre des évènements de client à l\'utilisateur',
								],
							]
						],
					]
				],
				'assos' => [
					'description' => 'Créer des évènements pour chaque association',
					'scopes' => [
						'created' => [
							'description' => 'Créer des évènements au nom d\'une association de l\'utilisateur',
						],
						'owned' => [
							'description' => 'Créer des évènements pour une association de l\'utilisateur',
							'scopes' => [
								'client' => [
									'description' => 'Créer des évènements pour des associations de l\'utilisateur au nom de mon application'
								],
								'asso' => [
									'description' => 'Créer les évènements pour chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'groups' => [
					'description' => 'Créer des évènements pour chaque groupe',
					'scopes' => [
						'created' => [
							'description' => 'Créer des évènements au nom d\'un groupe de l\'utilisateur',
						],
						'owned' => [
							'description' => 'Créer des évènements pour un groupe de l\'utilisateur',
							'scopes' => [
								'client' => [
									'description' => 'Créer des évènements pour des groupes de l\'utilisateur au nom de mon application'
								],
								'asso' => [
									'description' => 'Créer les évènements pour chaque groupe de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'clients' => [
					'description' => 'Créer des évènements pour chaque client',
					'scopes' => [
						'created' => [
							'description' => 'Créer des évènements au nom d\'un client de l\'utilisateur',
						],
						'owned' => [
							'description' => 'Créer des évènements pour un client de l\'utilisateur',
							'scopes' => [
								'client' => [
									'description' => 'Créer des évènements pour des applications de l\'utilisateur au nom de mon application'
								],
								'asso' => [
									'description' => 'Créer les évènements pour chaque application de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
			]
		],
	]
];
