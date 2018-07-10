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

// Toutes les routes commencant par client-{verbe}-calendar-
return [
	'description' => 'Calendrier',
	'verbs' => [
		'manage' => [
			'description' => 'Gérer tout les calendriers',
			'scopes' => [
				'users' => [
					'description' => 'Gérer les calendriers de chaque utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les calendriers que chaque utilisateur a créé',
						],
						'owned' => [
							'description' => 'Gérer les calendriers que chaque utilisateur possède',
							'scopes' => [
								'client' => [
									'description' => 'Gérer les calendriers que chaque utilisateur possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Gérer les calendriers que chaque utilisateur suit',
							'scopes' => [
								'users' => [
									'description' => 'Gérer les calendriers utilisateurs que chaque utilisateur suit',
								],
								'assos' => [
									'description' => 'Gérer les calendriers associatifs que chaque utilisateur suit',
								],
								'groups' => [
									'description' => 'Gérer les calendriers de groupe que chaque utilisateur suit',
								],
								'clients' => [
									'description' => 'Gérer les calendriers clients que chaque utilisateur suit',
								],
							]
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
					]
				],
			]
		],
		'get' => [
			'description' => 'Récupérer tout les calendriers',
			'scopes' => [
				'users' => [
					'description' => 'Récupérer les calendriers de chaque utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les calendriers que chaque utilisateur a créé',
						],
						'owned' => [
							'description' => 'Récupérer les calendriers que chaque utilisateur possède',
							'scopes' => [
								'client' => [
									'description' => 'Récupérer les calendriers que chaque utilisateur possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Récupérer les calendriers que chaque utilisateur suit',
							'scopes' => [
								'users' => [
									'description' => 'Récupérer les calendriers utilisateurs que chaque utilisateur suit',
								],
								'assos' => [
									'description' => 'Récupérer les calendriers associatifs que chaque utilisateur suit',
								],
								'groups' => [
									'description' => 'Récupérer les calendriers de groupe que chaque utilisateur suit',
								],
								'clients' => [
									'description' => 'Récupérer les calendriers clients que chaque utilisateur suit',
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
								]
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
								]
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
								]
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
					'description' => 'Modifier et créer les calendriers de chaque utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Modifier et créer les calendriers que chaque utilisateur a créé',
						],
						'owned' => [
							'description' => 'Modifier et créer les calendriers que chaque utilisateur possède',
							'scopes' => [
								'client' => [
									'description' => 'Modifier et créer les calendriers que chaque utilisateur possède et que mon client crée'
								]
							]
						],
						'followed' => [
							'description' => 'Modifier et créer les calendriers que chaque utilisateur suit',
							'scopes' => [
								'users' => [
									'description' => 'Modifier et créer les calendriers utilisateurs que chaque utilisateur suit',
								],
								'assos' => [
									'description' => 'Modifier et créer les calendriers associatifs que chaque utilisateur suit',
								],
								'groups' => [
									'description' => 'Modifier et créer les calendriers de groupe que chaque utilisateur suit',
								],
								'clients' => [
									'description' => 'Modifier et créer les calendriers clients que chaque utilisateur suit',
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
								]
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
								]
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
								]
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
					'description' => 'Créer et faire suivre des calendriers pour chaque utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Créer des calendriers pour chaque utilisateur',
						],
						'followed' => [
							'description' => 'Faire suivre des calendriers à chaque utilisateur',
							'scopes' => [
								'users' => [
									'description' => 'Faire suivre des calendriers utilisateurs à chaque utilisateur',
								],
								'assos' => [
									'description' => 'Faire suivre des calendriers associatifs à chaque utilisateur',
								],
								'groups' => [
									'description' => 'Faire suivre des calendriers de groupe à chaque utilisateur',
								],
								'clients' => [
									'description' => 'Faire suivre des calendriers de client à chaque utilisateur',
								],
							]
						],
					]
				],
				'assos' => [
					'description' => 'Créer des calendriers pour chaque association',
				],
				'groups' => [
					'description' => 'Créer des calendriers pour chaque groupe',
				],
				'clients' => [
					'description' => 'Créer des calendriers pour chaque client',
				],
			]
		],
	]
];
