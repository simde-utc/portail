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

// Toutes les routes commencant par user-{verbe}-rooms-
return [
  'description' => 'Salles',
  'icon' => 'door-open',
  'verbs' => [
    'manage' => [
      'description' => 'Gérer les salles',
      'scopes' => [
        'assos' => [
					'description' => 'Gérer les salles des associations de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les salles que chaque association de l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Gérer les salles que chaque association de l\'utilisateur possède',
							'scopes' => [
								'user' => [
									'description' => 'Gérer les salles que chaque association de l\'utilisateur possède et qu\'il crée'
								],
								'client' => [
									'description' => 'Gérer les salles que chaque association de l\'utilisateur possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Gérer les salles que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'users' => [
					'description' => 'Gérer les salles de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les salles que l\'utilisateur a créé',
						],
					]
				],
				'clients' => [
					'description' => 'Gérer les salles des clients de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Gérer les salles des clients que l\'utilisateur ont créé',
						],
					]
				],
      ]
    ],
    'set' => [
      'description' => 'Créer et modifier les salles',
      'scopes' => [
        'assos' => [
					'description' => 'Créer et modifier les salles des associations de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Créer et modifier les salles que chaque association de l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Créer et modifier les salles que chaque association de l\'utilisateur possède',
							'scopes' => [
								'user' => [
									'description' => 'Créer et modifier les salles que chaque association de l\'utilisateur possède et qu\'il crée'
								],
								'client' => [
									'description' => 'Créer et modifier les salles que chaque association de l\'utilisateur possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Créer et modifier les salles que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'users' => [
					'description' => 'Créer et modifier les salles de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Créer et modifier les salles que l\'utilisateur a créé',
						],
					]
				],
				'clients' => [
					'description' => 'Créer et modifier les salles des clients de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Créer et modifier les salles des clients que l\'utilisateur ont créé',
						],
					]
				],
      ]
    ],
    'get' => [
      'description' => 'Récupérer les salles',
      'scopes' => [
        'assos' => [
					'description' => 'Récupérer les salles des associations de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les salles que chaque association de l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Récupérer les salles que chaque association de l\'utilisateur possède',
							'scopes' => [
								'user' => [
									'description' => 'Récupérer les salles que chaque association de l\'utilisateur possède et qu\'il crée'
								],
								'client' => [
									'description' => 'Récupérer les salles que chaque association de l\'utilisateur possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Récupérer les salles que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'users' => [
					'description' => 'Récupérer les salles de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les salles que l\'utilisateur a créé',
						],
					]
				],
				'clients' => [
					'description' => 'Récupérer les salles des clients de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Récupérer les salles des clients que l\'utilisateur ont créé',
						],
					]
				],
      ]
    ],
    'create' => [
      'description' => 'Créer les salles',
      'scopes' => [
        'assos' => [
					'description' => 'Créer les salles des associations de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Créer les salles que chaque association de l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Créer les salles que chaque association de l\'utilisateur possède',
							'scopes' => [
								'user' => [
									'description' => 'Créer les salles que chaque association de l\'utilisateur possède et qu\'il crée'
								],
								'client' => [
									'description' => 'Créer les salles que chaque association de l\'utilisateur possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Créer les salles que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'users' => [
					'description' => 'Créer les salles de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Créer les salles que l\'utilisateur a créé',
						],
					]
				],
				'clients' => [
					'description' => 'Créer les salles des clients de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Créer les salles des clients que l\'utilisateur ont créé',
						],
					]
				],
      ]
    ],
    'edit' => [
      'description' => 'Modifier les salles',
      'scopes' => [
        'assos' => [
					'description' => 'Modifier les salles des associations de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Modifier les salles que chaque association de l\'utilisateur a créé',
						],
						'owned' => [
							'description' => 'Modifier les salles que chaque association de l\'utilisateur possède',
							'scopes' => [
								'user' => [
									'description' => 'Modifier les salles que chaque association de l\'utilisateur possède et qu\'il crée'
								],
								'client' => [
									'description' => 'Modifier les salles que chaque association de l\'utilisateur possède et que mon client crée'
								],
								'asso' => [
									'description' => 'Modifier les salles que chaque association de l\'utilisateur possède et que mon association crée'
								],
							]
						],
					]
				],
				'users' => [
					'description' => 'Modifier les salles de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Modifier les salles que l\'utilisateur a créé',
						],
					]
				],
				'clients' => [
					'description' => 'Modifier les salles des clients de l\'utilisateur',
					'scopes' => [
						'created' => [
							'description' => 'Modifier les salles des clients que l\'utilisateur ont créé',
						],
					]
				],
      ]
    ],
  ]
];
