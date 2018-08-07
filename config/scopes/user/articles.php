<?php

/*
 * Liste des scopes en fonction des routes
 *   - Définition des scopes:
 *      portée + "-" + verbe + "-" + categorie + (pour chaque sous-catégorie: '-' + sous-catégorie)
 *      ex: user-get-user user-get-user-assos user-get-user-assos-collaborated
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

// Toutes les routes commencant par user-{verbe}-articles-
return [
	'description' => 'Articles',
	'icon' => 'newspaper',
	'verbs' => [
		'manage' => [
			'description' => 'Gérer tout les articles',
			'scopes' => [
				'actions' => [
					'description' => 'Gérer les actions des articles',
					'scopes' => [
						'user' => [
							'description' => 'Gérer les actions de l\'utilisateur sur les articles',
						],
					]
				],
				'assos' => [
					'description' => 'Gérer les articles des associations de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les articles que chaque association de l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Gérer les articles que chaque association de l\'utilisateur possède',
							'scopes' => [
								'client' => [
									'description' => 'Gérer les articles que chaque association de l\'utilisateur possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Gérer les articles que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'groups' => [
					'description' => 'Gérer les articles de chaque groupe',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les articles que chaque groupe a créé',
						],
						'owned' => [
							'description' => 'Gérer les articles que chaque groupe possède',
							'scopes' => [
								'client' => [
									'description' => 'Gérer les articles que chaque groupe possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Gérer les articles que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
			]
		],
		'get' => [
			'description' => 'Récupérer tout les articles',
			'scopes' => [
				'actions' => [
					'description' => 'Récupérer les actions des articles',
					'scopes' => [
						'user' => [
							'description' => 'Récupérer les actions de l\'utilisateur sur les articles',
						],
					]
				],
				'assos' => [
					'description' => 'Récupérer les articles de chaque association',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les articles que chaque association a créé',
						],
						'owned' => [
							'description' => 'Récupérer les articles que chaque association possède',
							'scopes' => [
								'client' => [
									'description' => 'Récupérer les articles que chaque association possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Récupérer les articles que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'groups' => [
					'description' => 'Récupérer les articles de chaque groupe',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les articles que chaque groupe a créé',
						],
						'owned' => [
							'description' => 'Récupérer les articles que chaque groupe possède',
							'scopes' => [
								'client' => [
									'description' => 'Récupérer les articles que chaque groupe possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Récupérer les articles que chaque groupe de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
			]
		],
		'set' => [
			'description' => 'Modifier et créer les articles',
			'scopes' => [
				'actions' => [
					'description' => 'Modifier et créer les actions des articles',
					'scopes' => [
						'user' => [
							'description' => 'Modifier et créer les actions de l\'utilisateur sur les articles',
						],
					]
				],
				'assos' => [
					'description' => 'Modifier et créer les articles de chaque association',
					'scopes' => [
						'created' => [
							'description' => 'Modifier et créer les articles que chaque association a créé',
						],
						'owned' => [
							'description' => 'Modifier et créer les articles que chaque association possède',
							'scopes' => [
								'client' => [
									'description' => 'Modifier et créer les articles que chaque association possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Modifier et créer les articles que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'groups' => [
					'description' => 'Modifier et créer les articles de chaque groupe',
					'scopes' => [
						'created' => [
							'description' => 'Modifier et créer les articles que chaque groupe a créé',
						],
						'owned' => [
							'description' => 'Modifier et créer les articles que chaque groupe possède',
							'scopes' => [
								'client' => [
									'description' => 'Modifier et créer les articles que chaque groupe possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Modifier et créer les articles que chaque groupe de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
			]
		],
		'edit' => [
			'description' => 'Modifier les articles',
			'scopes' => [
				'actions' => [
					'description' => 'Modifier les actions des articles',
					'scopes' => [
						'user' => [
							'description' => 'Modifier les actions de l\'utilisateur sur les articles',
						],
					]
				],
				'assos' => [
					'description' => 'Modifier les articles de chaque association',
					'scopes' => [
						'created' => [
							'description' => 'Modifier les articles que chaque association a créé',
						],
						'owned' => [
							'description' => 'Modifier les articles que chaque association possède',
							'scopes' => [
								'client' => [
									'description' => 'Modifier les articles que chaque association possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Modifier les articles que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'groups' => [
					'description' => 'Modifier les articles de chaque groupe',
					'scopes' => [
						'created' => [
							'description' => 'Modifier les articles que chaque groupe a créé',
						],
						'owned' => [
							'description' => 'Modifier les articles que chaque groupe possède',
							'scopes' => [
								'client' => [
									'description' => 'Modifier les articles que chaque groupe possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Modifier les articles que chaque groupe de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
			]
		],
		'create' => [
			'description' => 'Créer et faire suivre des articles',
			'scopes' => [
				'actions' => [
					'description' => 'Créer les actions des articles',
					'scopes' => [
						'user' => [
							'description' => 'Créer les actions de l\'utilisateur sur les articles',
						],
					]
				],
				'assos' => [
					'description' => 'Créer des articles pour chaque association',
					'scopes' => [
						'created' => [
							'description' => 'Créer des articles au nom d\'une association de l\'utilisateur',
						],
						'owned' => [
							'description' => 'Créer des articles pour une association de l\'utilisateur',
							'scopes' => [
								'client' => [
									'description' => 'Créer des articles pour des associations de l\'utilisateur au nom de mon application'
								],
								'asso' => [
									'description' => 'Créer les articles pour chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'groups' => [
					'description' => 'Créer des articles pour chaque groupe',
					'scopes' => [
						'created' => [
							'description' => 'Créer des articles au nom d\'un groupe de l\'utilisateur',
						],
						'owned' => [
							'description' => 'Créer des articles pour un groupe de l\'utilisateur',
							'scopes' => [
								'client' => [
									'description' => 'Créer des articles pour des groupes de l\'utilisateur au nom de mon application'
								],
								'asso' => [
									'description' => 'Créer les articles pour chaque groupe de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
			]
		],
	]
];
