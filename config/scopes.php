<?php

/*
 * Liste des scopes en fonction des routes
 *   - Définition des scopes:
 *   	portée + "-" + verbe + "-" + categorie + (pour chaque sous-catégorie: '-' + sous-catégorie)
 *   	ex: u-get-user u-get-user-assos u-get-user-assos-followed
 *
 *   - Définition de la portée des scopes:
 *     + u :     user_credential => nécessite que l'application soit connecté à un utilisateur
 *     + c :     client_credential => nécessite que l'application est les droits d'application indépendante d'un utilisateur
 *
 *   - Définition du verbe:
 *     + manage:  gestion de la ressource entière
 *       + get :   récupération des informations en lecture seule
 *       + set :   posibilité d'écrire et modifier les données
 *         + create:   créer une donnée associée
 *         + edit:    modifier une donnée
 *         + remove:  supprimer une donnée
 */
return [
	// L'application DOIT être connectée via un utilisateur
	'u' => [
		// Gestion relative à l'utilisateur
		'user' => [
			'description' => 'Permet à l\'application de posséder différentes actions sur tous les utilisateurs',
			'verbs' => [
				'manage' => [
					'description' => 'Gérer totalement les données de l\'utilisateur',
					'scopes' => [
						'assos' => [
							'description' => 'Gérer les associations suivies et faites par l\'utilisateur',
							'scopes' => [
								'done' => [
									'description' => 'Gérer les associations faites par l\'utilisateur',
									'scopes' => [
										'now' => [
											'description' => 'Gérer les associations faites par l\'utilisateur durant l\'actuel semestre',
										],
									]
								],
								'followed' => [
									'description' => 'Gérer les associations suivies par l\'utilisateur',
									'scopes' => [
										'now' => [
											'description' => 'Gérer les associations suivies par l\'utilisateur durant l\'actuel semestre',
										],
									]
								],
							]
						]
					]
				],
				'get' => [
					'description' => 'Récupérer toutes les informations de l\'utilisateur',
					'scopes' => [
						'assos' => [
							'description' => 'Récupérer toutes les associations suivies et faites par l\'utilisateur',
							'scopes' => [
								'done' => [
									'description' => 'Récupérer toutes les associations faites par l\'utilisateur',
									'scopes' => [
										'now' => [
											'description' => 'Récupérer toutes les associations faites par l\'utilisateur durant l\'actuel semestre',
										],
									]
								],
								'followed' => [
									'description' => 'Récupérer toutes les associations suivies par l\'utilisateur',
									'scopes' => [
										'now' => [
											'description' => 'Récupérer toutes les associations suivies par l\'utilisateur durant l\'actuel semestre',
										],
									]
								],
								'members' => [
									'description' => 'Récupérer tous les membres des associations suivies et faites par l\'utilisateur',
									'scopes' => [
										'now' => [
											'description' => 'Récupérer tous les membres des associations suivies et faites par l\'utilisateur durant l\'actuel semestre',
										],
									]
								],
							]
						]
					]
				],
				'set' => [
					'description' => "Modifier toutes les données de l'utilisateur",
					'scopes' => [
						'assos' => [
							'description' => 'Créer et modifier les associations suivies et faites par l\'utilisateur',
							'scopes' => [
								'done' => [
									'description' => 'Créer et modifier les associations faites par l\'utilisateur',
									'scopes' => [
										'now' => [
											'description' => 'Créer et modifier les associations faites par l\'utilisateur durant l\'actuel semestre',
										],
									]
								],
								'followed' => [
									'description' => 'Créer et modifier les associations suivies par l\'utilisateur',
									'scopes' => [
										'now' => [
											'description' => 'Créer et modifier les associations suivies par l\'utilisateur durant l\'actuel semestre',
										],
									]
								],
							]
						]
					]
				],
			]
		],

		// Gestion relative au calendrier
		'calendar' => [
			'description' => 'Permet à l\'application de posséder des informations sur le calendrier universitaire',
			'verbs' => [
				'get' => [ // scope: c-get-calendar
					'description' => 'Récupérer le calendrier universitaire',
					'scopes' => [
						'semester' => [ // scope: c-get-calendar-semester
							'description' => 'Récupérer l\'actuel semestre universitaire'
						],
						'semesters' => [ // scope: c-get-calendar-semesters
							'description' => 'Récupérer les différents semestres universitaires'
						],
						'year' => [ // scope: c-get-calendar-year
							'description' => 'Récupérer l\'actuel année universitaire'
						],
						'years' => [ // scope: c-get-calendar-years
							'description' => 'Récupérer les différentes années universitaires'
						]
					]
				]
			]
		],
	],

	// L'application NE DOIT PAS être connectée via un utilisateur
	'c' => [
		// Gestion relative aux utilisateurs
		'users' => [
			'description' => 'Permet à l\'application de posséder différentes actions sur tous les utilisateurs',
			'verbs' => [
				'manage' => [
					'description' => 'Gérer totalement tous les utilisateurs',
				],
				'get' => [
					'description' => 'Récupérer la liste des utilisateurs',
				],
				'set' => [
					'description' => "Gérer la création et la modification d'utilisateurs",
				],
			]
		],

		// Gestion relative aux associations
		'assos' => [
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
		],

		// Gestion relative au calendrier
		'calendar' => [
			'description' => 'Permet à l\'application de posséder des informations sur le calendrier universitaire',
			'verbs' => [
				'get' => [ // scope: c-get-calendar
					'description' => 'Récupérer le calendrier universitaire',
					'scopes' => [
						'semester' => [ // scope: c-get-calendar-semester
							'description' => 'Récupérer l\'actuel semestre universitaire'
						],
						'semesters' => [ // scope: c-get-calendar-semesters
							'description' => 'Récupérer les différents semestres universitaires'
						],
						'year' => [ // scope: c-get-calendar-year
							'description' => 'Récupérer l\'actuel année universitaire'
						],
						'years' => [ // scope: c-get-calendar-years
							'description' => 'Récupérer les différentes années universitaires'
						]
					]
				]
			]
		],
	],

	// Surement d'autres à ajouter pour chaque section haha
];
