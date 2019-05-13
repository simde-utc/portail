<?php
/**
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

// Toutes les routes commencant par user-{verbe}-permissions-
return [
	'description' => 'Permissions',
	'icon' => 'gavel',
	'verbs' => [
		'manage' => [
			'description' => 'Gérer les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes, permissions rôle)',
			'scopes' => [
				'users' => [
					'description' => 'Gérer les permissions et les assigner à l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Gérer les permissions de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Gérer les permissions assignés à l\'utilisateur',
						],
					]
				],
				'assos' => [
					'description' => 'Gérer les permissions et les assigner aux associations de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Gérer les permissions des associations de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Gérer les permissions assignés aux associations de l\'utilisateur',
						],
					]
				],
				'groups' => [
					'description' => 'Gérer les permissions et les assigner aux groupes de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Gérer les permissions des groupes de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Gérer les permissions assignés aux groupes de l\'utilisateur',
						],
					]
				],
				'roles' => [
					'description' => 'Gérer les permissions et les assigner à des rôles',
					'scopes' => [
						'owned' => [
							'description' => 'Gérer les permissions d\'un rôle',
						],
						'assigned' => [
							'description' => 'Gérer les permissions assignées à un rôle',
						],
					]
				],
		
			]
		],
		'get' => [
			'description' => 'Récupérer les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes, permissions rôle)',
			'scopes' => [
				'users' => [
					'description' => 'Récupérer les permissions et les assigner à l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Récupérer les permissions de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Récupérer les permissions assignés à l\'utilisateur',
						],
					]
				],
				'assos' => [
					'description' => 'Récupérer les permissions et les assigner aux associations de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Récupérer les permissions des associations de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Récupérer les permissions assignés aux associations de l\'utilisateur',
						],
					]
				],
				'groups' => [
					'description' => 'Récupérer les permissions et les assigner aux groupes de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Récupérer les permissions des groupes de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Récupérer les permissions assignés aux groupes de l\'utilisateur',
						],
					]
				],
				'roles' => [
					'description' => 'Récupérer des permissions et les assigner à des rôles',
					'scopes' => [
						'owned' => [
							'description' => 'Récupérer les permissions d\'un rôle',
						],
						'assigned' => [
							'description' => 'Récupérer les permissions assignées à un rôle',
						],
					]
				],
	    
			]
		],
		'set' => [
			'description' => 'Créer et modifier les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes, permissions rôles)',
			'scopes' => [
				'users' => [
					'description' => 'Créer et modifier les permissions et les assigner à l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Créer et modifier les permissions de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Créer et modifier les permissions assignés à l\'utilisateur',
						],
					]
				],
				'assos' => [
					'description' => 'Créer et modifier les permissions et les assigner aux associations de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Créer et modifier les permissions des associations de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Créer et modifier les permissions assignés aux associations de l\'utilisateur',
						],
					]
				],
				'groups' => [
					'description' => 'Créer et modifier les permissions et les assigner aux groupes de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Créer et modifier les permissions des groupes de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Créer et modifier les permissions assignés aux groupes de l\'utilisateur',
						],
					]
				],
				'roles' => [
					'description' => 'Créer et modifier les permissions et les assigner à des rôles',
					'scopes' => [
						'owned' => [
							'description' => 'Créer et modifier les permissions des rôles',
						],
						'assigned' => [
							'description' => 'Créer et modifier les permissions assignées aux rôles',
						],
					]
				],
			],
		],
		'edit' => [
			'description' => 'Modifier les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes, permissions rôles)',
			'scopes' => [
				'users' => [
					'description' => 'Modifier les permissions et les assigner à l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Modifier les permissions de l\'utilisateur',
						],
						'assigned' => [
					'description' => 'Modifier les permissions assignés à l\'utilisateur',
							],
						]
					],
					'assos' => [
						'description' => 'Modifier les permissions et les assigner aux associations de l\'utilisateur',
						'scopes' => [
							'owned' => [
								'description' => 'Modifier les permissions des associations de l\'utilisateur',
							],
							'assigned' => [
								'description' => 'Modifier les permissions assignés aux associations de l\'utilisateur',
							],
						]
					],
					'groups' => [
						'description' => 'Modifier les permissions et les assigner aux groupes de l\'utilisateur',
						'scopes' => [
							'owned' => [
								'description' => 'Modifier les permissions des groupes de l\'utilisateur',
							],
							'assigned' => [
								'description' => 'Modifier les permissions assignés aux groupes de l\'utilisateur',
							],
						]
					],
					'roles' => [
						'description' => 'Modifier les permissions et les assigner à des rôles',
						'scopes' => [
							'owned' => [
								'description' => 'Modifier les permissions des rôles',
							],
							'assigned' => [
								'description' => 'Modifier les permissions assignées aux rôles',
							],
						]
					],
		    
				]
			],
			'create' => [
				'description' => 'Créer les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes, permissions rôles)',
				'scopes' => [
					'users' => [
						'description' => 'Créer les permissions et les assigner à l\'utilisateur',
						'scopes' => [
							'owned' => [
								'description' => 'Créer les permissions de l\'utilisateur',
							],
							'assigned' => [
								'description' => 'Créer les permissions assignés à l\'utilisateur',
							],
						]
					],
					'assos' => [
						'description' => 'Créer les permissions et les assigner aux associations de l\'utilisateur',
						'scopes' => [
							'owned' => [
								'description' => 'Créer les permissions des associations de l\'utilisateur',
							],
							'assigned' => [
								'description' => 'Créer les permissions assignés aux associations de l\'utilisateur',
							],
						]
					],
					'groups' => [
						'description' => 'Créer les permissions et les assigner aux groupes de l\'utilisateur',
						'scopes' => [
							'owned' => [
								'description' => 'Créer les permissions des groupes de l\'utilisateur',
							],
							'assigned' => [
								'description' => 'Créer les permissions assignés aux groupes de l\'utilisateur',
							],
						]
					],
					'roles' => [
						'description' => 'Créer les permissions et les assigner à des rôles',
						'scopes' => [
							'owned' => [
								'description' => 'Créer les permissions des rôles',
							],
							'assigned' => [
								'description' => 'Créer les permissions assignées aux rôles',
							],
						]
			
					],
				]
			],
			'remove' => [
				'description' => 'Supprimer les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes, permissions rôle)',
				'scopes' => [
					'users' => [
						'description' => 'Supprimer les permissions et les assigner à l\'utilisateur',
						'scopes' => [
							'owned' => [
								'description' => 'Supprimer les permissions de l\'utilisateur',
							],
							'assigned' => [
								'description' => 'Supprimer les permissions assignés à l\'utilisateur',
							],
						]
					],
					'assos' => [
						'description' => 'Supprimer les permissions et les assigner aux associations de l\'utilisateur',
						'scopes' => [
							'owned' => [
								'description' => 'Supprimer les permissions des associations de l\'utilisateur',
							],
							'assigned' => [
								'description' => 'Supprimer les permissions assignés aux associations de l\'utilisateur',
							],
						]
					],
					'groups' => [
						'description' => 'Supprimer les permissions et les assigner aux groupes de l\'utilisateur',
						'scopes' => [
							'owned' => [
								'description' => 'Supprimer les permissions des groupes de l\'utilisateur',
							],
							'assigned' => [
								'description' => 'Supprimer les permissions assignés aux groupes de l\'utilisateur',
							],
						]
					],
					'roles' => [
						'description' => 'Supprimer les permissions et les permissions assignées à des rôles',
						'scopes' => [
							'owned' => [
								'description' => 'Supprimer les permissions des rôles',
							],
							'assigned' => [
								'description' => 'Supprimer les permissions assignées aux rôles',
							],
						]
					],
				]
			],
		]
	];

