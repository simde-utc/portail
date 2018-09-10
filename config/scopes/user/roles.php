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
      'description' => 'Gérer les rôles et les assigner (rôles systèmes, rôles associations, rôles groupes)',
			'scopes' => [
        'users' => [
					'description' => 'Gérer les rôles et les assigner à l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Gérer les rôles de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Gérer les rôles assignés à l\'utilisateur',
						],
					]
        ],
        'assos' => [
					'description' => 'Gérer les rôles et les assigner aux associations de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Gérer les rôles des associations de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Gérer les rôles assignés aux associations de l\'utilisateur',
						],
					]
        ],
        'groups' => [
					'description' => 'Gérer les rôles et les assigner aux groupes de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Gérer les rôles des groupes de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Gérer les rôles assignés aux groupes de l\'utilisateur',
						],
					]
        ],
			]
    ],
    'get' => [
      'description' => 'Récupérer les rôles et les assigner (rôles systèmes, rôles associations, rôles groupes)',
			'scopes' => [
        'users' => [
					'description' => 'Récupérer les rôles et les assigner à l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Récupérer les rôles de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Récupérer les rôles assignés à l\'utilisateur',
						],
					]
        ],
        'assos' => [
					'description' => 'Récupérer les rôles et les assigner aux associations de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Récupérer les rôles des associations de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Récupérer les rôles assignés aux associations de l\'utilisateur',
						],
					]
        ],
        'groups' => [
					'description' => 'Récupérer les rôles et les assigner aux groupes de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Récupérer les rôles des groupes de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Récupérer les rôles assignés aux groupes de l\'utilisateur',
						],
					]
        ],
			]
    ],
    'set' => [
      'description' => 'Créer et modifier les rôles et les assigner (rôles systèmes, rôles associations, rôles groupes)',
			'scopes' => [
        'users' => [
					'description' => 'Créer et modifier les rôles et les assigner à l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Créer et modifier les rôles de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Créer et modifier les rôles assignés à l\'utilisateur',
						],
					]
        ],
        'assos' => [
					'description' => 'Créer et modifier les rôles et les assigner aux associations de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Créer et modifier les rôles des associations de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Créer et modifier les rôles assignés aux associations de l\'utilisateur',
						],
					]
        ],
        'groups' => [
					'description' => 'Créer et modifier les rôles et les assigner aux groupes de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Créer et modifier les rôles des groupes de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Créer et modifier les rôles assignés aux groupes de l\'utilisateur',
						],
					]
        ],
			]
    ],
    'edit' => [
      'description' => 'Modifier les rôles et les assigner (rôles systèmes, rôles associations, rôles groupes)',
			'scopes' => [
        'users' => [
					'description' => 'Modifier les rôles et les assigner à l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Modifier les rôles de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Modifier les rôles assignés à l\'utilisateur',
						],
					]
        ],
        'assos' => [
					'description' => 'Modifier les rôles et les assigner aux associations de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Modifier les rôles des associations de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Modifier les rôles assignés aux associations de l\'utilisateur',
						],
					]
        ],
        'groups' => [
					'description' => 'Modifier les rôles et les assigner aux groupes de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Modifier les rôles des groupes de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Modifier les rôles assignés aux groupes de l\'utilisateur',
						],
					]
        ],
			]
    ],
    'create' => [
      'description' => 'Créer les rôles et les assigner (rôles systèmes, rôles associations, rôles groupes)',
			'scopes' => [
        'users' => [
					'description' => 'Créer les rôles et les assigner à l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Créer les rôles de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Créer les rôles assignés à l\'utilisateur',
						],
					]
        ],
        'assos' => [
					'description' => 'Créer les rôles et les assigner aux associations de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Créer les rôles des associations de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Créer les rôles assignés aux associations de l\'utilisateur',
						],
					]
        ],
        'groups' => [
					'description' => 'Créer les rôles et les assigner aux groupes de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Créer les rôles des groupes de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Créer les rôles assignés aux groupes de l\'utilisateur',
						],
					]
        ],
			]
    ],
    'remove' => [
      'description' => 'Supprimer les rôles et les assigner (rôles systèmes, rôles associations, rôles groupes)',
			'scopes' => [
        'users' => [
					'description' => 'Supprimer les rôles et les assigner à l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Supprimer les rôles de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Supprimer les rôles assignés à l\'utilisateur',
						],
					]
        ],
        'assos' => [
					'description' => 'Supprimer les rôles et les assigner aux associations de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Supprimer les rôles des associations de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Supprimer les rôles assignés aux associations de l\'utilisateur',
						],
					]
        ],
        'groups' => [
					'description' => 'Supprimer les rôles et les assigner aux groupes de l\'utilisateur',
					'scopes' => [
						'owned' => [
							'description' => 'Supprimer les rôles des groupes de l\'utilisateur',
						],
						'assigned' => [
							'description' => 'Supprimer les rôles assignés aux groupes de l\'utilisateur',
						],
					]
        ],
			]
    ],
	]
];
