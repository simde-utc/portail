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

// Toutes les routes commencant par user-{verbe}-assos-
return [
	'description' => 'Associations',
	'icon' => 'handshake',
	'verbs' => [
		'manage' => [
			'description' => 'Gérer au nom de l\'utilisateur les associations et leurs membres',
			'scopes' => [
				'members' => [
					'description' => 'Gérer les associations que l\'utilisateur suit ou fait',
					'scopes' => [
						'joined' => [
							'description' => 'Gérer les associations que l\'utilisateur fait',
							'scopes' => [
								'now' => [
									'description' => 'Gérer les associations que l\'utilisateur fait durant l\'actuel semestre',
								],
							]
						],
						'joining' => [
							'description' => 'Gérer les associations que l\'utilisateur souhaite rejoindre',
							'scopes' => [
								'now' => [
									'description' => 'Gérer les associations que l\'utilisateur souhaite rejoindre durant l\'actuel semestre',
								],
							]
						],
						'followed' => [
							'description' => 'Gérer les associations que l\'utilisateur suit',
							'scopes' => [
								'now' => [
									'description' => 'Gérer les associations que l\'utilisateur suit durant l\'actuel semestre',
								],
							]
						],
					]
				],
			]
		],
		'get' => [
			'description' => 'Récupérer au nom de l\'utilisateur les associations et leurs membres',
			'scopes' => [
				'members' => [
					'description' => 'Récupérer toutes les associations que l\'utilisateur suit ou fait',
					'scopes' => [
						'joined' => [
							'description' => 'Récupérer toutes les associations que l\'utilisateur fait',
							'scopes' => [
								'now' => [
									'description' => 'Récupérer toutes les associations que l\'utilisateur fait durant l\'actuel semestre',
								],
							]
						],
						'joining' => [
							'description' => 'Récupérer toutes les associations que l\'utilisateur souhaite rejoindre',
							'scopes' => [
								'now' => [
									'description' => 'Récupérer toutes les associations que l\'utilisateur souhaite rejoindre durant l\'actuel semestre',
								],
							]
						],
						'followed' => [
							'description' => 'Récupérer toutes les associations que l\'utilisateur suit',
							'scopes' => [
								'now' => [
									'description' => 'Récupérer toutes les associations que l\'utilisateur suit durant l\'actuel semestre',
								],
							]
						],
						'members' => [
							'description' => 'Récupérer tous les membres des associations que l\'utilisateur suit ou fait',
							'scopes' => [
								'now' => [
									'description' => 'Récupérer tous les membres des associations que l\'utilisateur suit ou fait durant l\'actuel semestre',
								],
							]
						],
					]
				],
			]
		],
		'set' => [
			'description' => 'Ajouter et modifier au nom de l\'utilisateur les associations et leurs membres',
			'scopes' => [
				'members' => [
					'description' => 'Ajouter et modifier les associations que l\'utilisateur suit ou fait',
					'scopes' => [
						'joined' => [
							'description' => 'Ajouter et modifier les associations que l\'utilisateur fait',
							'scopes' => [
								'now' => [
									'description' => 'Ajouter et modifier les associations que l\'utilisateur fait durant l\'actuel semestre',
								],
							]
						],
						'joining' => [
							'description' => 'Ajouter et modifier les associations que l\'utilisateur souhaite rejoindre',
							'scopes' => [
								'now' => [
									'description' => 'Ajouter et modifier les associations que l\'utilisateur souhaite rejoindre durant l\'actuel semestre',
								],
							]
						],
						'followed' => [
							'description' => 'Ajouter et modifier les associations que l\'utilisateur suit',
							'scopes' => [
								'now' => [
									'description' => 'Ajouter et modifier les associations que l\'utilisateur suit durant l\'actuel semestre',
								],
							]
						],
					]
				],
			]
		],
		'edit' => [
			'description' => 'Modifier au nom de l\'utilisateur les associations et leurs membres',
			'scopes' => [
				'members' => [
					'description' => 'Modifier les associations que l\'utilisateur suit ou fait',
					'scopes' => [
						'joined' => [
							'description' => 'Modifier les associations que l\'utilisateur fait',
							'scopes' => [
								'now' => [
									'description' => 'Modifier les associations que l\'utilisateur fait durant l\'actuel semestre',
								],
							]
						],
						'joining' => [
							'description' => 'Modifier les associations que l\'utilisateur souhaite rejoindre',
							'scopes' => [
								'now' => [
									'description' => 'Modifier les associations que l\'utilisateur souhaite rejoindre durant l\'actuel semestre',
								],
							]
						],
						'followed' => [
							'description' => 'Modifier les associations que l\'utilisateur suit',
							'scopes' => [
								'now' => [
									'description' => 'Modifier les associations que l\'utilisateur suit durant l\'actuel semestre',
								],
							]
						],
					]
				],
			]
		],
		'create' => [
			'description' => 'Créer au nom de l\'utilisateur des associations et ajouter des membres aux associations',
			'scopes' => [
				'members' => [
					'description' => 'Ajouter des associations que l\'utilisateur suit ou fait',
					'scopes' => [
						'joined' => [
							'description' => 'Ajouter des associations que l\'utilisateur fait',
							'scopes' => [
								'now' => [
									'description' => 'Ajouter des associations que l\'utilisateur fait durant l\'actuel semestre',
								],
							]
						],
						'joining' => [
							'description' => 'Ajouter des associations que l\'utilisateur souhaite rejoindre',
							'scopes' => [
								'now' => [
									'description' => 'Ajouter des associations que l\'utilisateur souhaite rejoindre durant l\'actuel semestre',
								],
							]
						],
						'followed' => [
							'description' => 'Ajouter des associations que l\'utilisateur suit',
							'scopes' => [
								'now' => [
									'description' => 'Ajouter des associations que l\'utilisateur suit durant l\'actuel semestre',
								],
							]
						],
					]
				],
			]
		],
		'remove' => [
			'description' => 'Supprimer au nom de l\'utilisateur des associations et retirer des membres aux associations',
			'scopes' => [
				'members' => [
					'description' => 'Retirer des associations que l\'utilisateur suit ou fait',
					'scopes' => [
						'joined' => [
							'description' => 'Retirer des associations que l\'utilisateur fait',
							'scopes' => [
								'now' => [
									'description' => 'Retirer des associations que l\'utilisateur fait durant l\'actuel semestre',
								],
							]
						],
						'joining' => [
							'description' => 'Retirer des associations que l\'utilisateur souhaite rejoindre',
							'scopes' => [
								'now' => [
									'description' => 'Retirer des associations que l\'utilisateur souhaite rejoindre durant l\'actuel semestre',
								],
							]
						],
						'followed' => [
							'description' => 'Retirer des associations que l\'utilisateur suit',
							'scopes' => [
								'now' => [
									'description' => 'Retirer des associations que l\'utilisateur suit durant l\'actuel semestre',
								],
							]
						],
					]
				],
			]
		],
	]
];
