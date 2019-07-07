<?php
/**
 * List of scopes depending on routes
 *   - Scopes definition:
 *      range + "-" + verb + "-" + category + (for each subcategory: '-' + subcategory)
 *      ex: user-get-user user-get-user-assos user-get-user-assos-collaborated
 *
 *   - Scope range definition:
 *     + user :    user_credential => nécessite que l'application soit connecté à un utilisateur
 *     + client :  client_credential => nécessite que l'application est les droits d'application indépendante d'un utilisateur
 *
 *   - Définition du verbe:
 *     + manage:  Entire ressource management.
 *       + set :  Possibility of writing/updating data.
 *         + get :  Read-only data retrievement
 *         + create:  New data creation
 *         + edit:    update data
 *       + remove:  delete data
 */

// Toutes les routes commencant par client-{verbe}-events-
return [
    'description' => 'Evènements',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer tout les évènements',
            'scopes' => [
                'users' => [
                    'description' => 'Gérer les évènements de chaque utilisateur',
                    'scopes' => [
                        'created' => [
                            'description' => 'Gérer les évènements que chaque utilisateur a créé',
                        ],
                        'owned' => [
                            'description' => 'Gérer les évènements que chaque utilisateur possède',
                            'scopes' => [
                                'client' => [
                                    'description' => 'Gérer les évènements que chaque utilisateur possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Gérer les évènements que chaque utilisateur possède et que mon association crée',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Gérer les évènements que chaque utilisateur suit',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Gérer les évènements utilisateurs que chaque utilisateur suit',
                                ],
                                'asso' => [
                                    'description' => 'Gérer les évènements associatifs que chaque utilisateur suit',
                                ],
                                'group' => [
                                    'description' => 'Gérer les évènements de groupe que chaque utilisateur suit',
                                ],
                                'client' => [
                                    'description' => 'Gérer les évènements clients que chaque utilisateur suit',
                                ],
                            ]
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Gérer les évènements de chaque association',
                    'scopes' => [
                        'created' => [
                            'description' => 'Gérer les évènements que chaque association a créé',
                        ],
                        'owned' => [
                            'description' => 'Gérer les évènements que chaque association possède',
                            'scopes' => [
                                'client' => [
                                    'description' => 'Gérer les évènements que chaque association possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Gérer les évènements que chaque association possède et que mon association crée',
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
                                    'description' => 'Gérer les évènements que chaque groupe possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Gérer les évènements que chaque groupe possède et que mon association crée',
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
                                    'description' => 'Gérer les évènements que chaque client possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Gérer les évènements que chaque client possède et que mon association crée',
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
                    'description' => 'Récupérer les évènements de chaque utilisateur',
                    'scopes' => [
                        'created' => [
                            'description' => 'Récupérer les évènements que chaque utilisateur a créé',
                        ],
                        'owned' => [
                            'description' => 'Récupérer les évènements que chaque utilisateur possède',
                            'scopes' => [
                                'client' => [
                                    'description' => 'Récupérer les évènements que chaque utilisateur possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Récupérer les évènements que chaque utilisateur possède et que mon association crée',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Récupérer les évènements que chaque utilisateur suit',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Récupérer les évènements utilisateurs que chaque utilisateur suit',
                                ],
                                'asso' => [
                                    'description' => 'Récupérer les évènements associatifs que chaque utilisateur suit',
                                ],
                                'group' => [
                                    'description' => 'Récupérer les évènements de groupe que chaque utilisateur suit',
                                ],
                                'client' => [
                                    'description' => 'Récupérer les évènements clients que chaque utilisateur suit',
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
                                    'description' => 'Récupérer les évènements que chaque association possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Récupérer les évènements que chaque association possède et que mon association crée',
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
                                    'description' => 'Récupérer les évènements que chaque groupe possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Récupérer les évènements que chaque groupe possède et que mon association crée',
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
                                    'description' => 'Récupérer les évènements que chaque client possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Récupérer les évènements que chaque client possède et que mon association crée',
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
                    'description' => 'Modifier et créer les évènements de chaque utilisateur',
                    'scopes' => [
                        'created' => [
                            'description' => 'Modifier et créer les évènements que chaque utilisateur a créé',
                        ],
                        'owned' => [
                            'description' => 'Modifier et créer les évènements que chaque utilisateur possède',
                            'scopes' => [
                                'client' => [
                                    'description' => 'Modifier et créer les évènements que chaque utilisateur possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Modifier et créer les évènements que chaque utilisateur possède et que mon association crée',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Modifier et créer les évènements que chaque utilisateur suit',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Modifier et créer les évènements utilisateurs que chaque utilisateur suit',
                                ],
                                'asso' => [
                                    'description' => 'Modifier et créer les évènements associatifs que chaque utilisateur suit',
                                ],
                                'group' => [
                                    'description' => 'Modifier et créer les évènements de groupe que chaque utilisateur suit',
                                ],
                                'client' => [
                                    'description' => 'Modifier et créer les évènements clients que chaque utilisateur suit',
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
                                    'description' => 'Modifier et créer les évènements que chaque association possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Modifier et créer les évènements que chaque association possède et que mon association crée',
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
                                    'description' => 'Modifier et créer les évènements que chaque groupe possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Modifier et créer les évènements que chaque groupe possède et que mon association crée',
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
                                    'description' => 'Modifier et créer les évènements que chaque client possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Modifier et créer les évènements que chaque client possède et que mon association crée',
                                ],
                            ]
                        ],
                    ]
                ],
            ]
        ],
        'create' => [
            'description' => 'Créer et faire suivre des évènements',
            'users' => [
                'description' => 'Créer et faire suivre des évènements pour chaque utilisateur',
                'scopes' => [
                    'created' => [
                        'description' => 'Créer des évènements au nom de chaque utilisateur',
                    ],
                    'owned' => [
                        'description' => 'Créer des évènements pour chaque utilisateur',
                        'scopes' => [
                            'client' => [
                                'description' => 'Créer des évènements pour chaque utilisateur au nom de mon application',
                            ],
                            'asso' => [
                                'description' => 'Créer les évènements pour chaque utilisateur au nom de mon association',
                            ],
                        ]
                    ],
                    'followed' => [
                        'description' => 'Faire suivre des évènements à chaque utilisateur',
                        'scopes' => [
                            'user' => [
                                'description' => 'Faire suivre des évènements utilisateurs à chaque utilisateur',
                            ],
                            'asso' => [
                                'description' => 'Faire suivre des évènements associatifs à chaque utilisateur',
                            ],
                            'group' => [
                                'description' => 'Faire suivre des évènements de groupe à chaque utilisateur',
                            ],
                            'client' => [
                                'description' => 'Faire suivre des évènements de client à chaque utilisateur',
                            ],
                        ]
                    ],
                ]
            ],
            'assos' => [
                'description' => 'Créer des évènements pour chaque association',
                'scopes' => [
                    'created' => [
                        'description' => 'Créer des évènements au nom d\'une association de chaque utilisateur',
                    ],
                    'owned' => [
                        'description' => 'Créer des évènements pour une association de chaque utilisateur',
                        'scopes' => [
                            'client' => [
                                'description' => 'Créer des évènements pour des associations de chaque utilisateur au nom de mon application',
                            ],
                            'asso' => [
                                'description' => 'Créer les évènements pour des associations de chaque utilisateur au nom de mon association',
                            ],
                        ]
                    ],
                ]
            ],
            'groups' => [
                'description' => 'Créer des évènements pour chaque groupe',
                'scopes' => [
                    'created' => [
                        'description' => 'Créer des évènements au nom d\'un groupe de chaque utilisateur',
                    ],
                    'owned' => [
                        'description' => 'Créer des évènements pour un groupe de chaque utilisateur',
                        'scopes' => [
                            'client' => [
                                'description' => 'Créer des évènements pour des groupes de chaque utilisateur au nom de mon application',
                            ],
                            'asso' => [
                                'description' => 'Créer les évènements pour des groupes de chaque utilisateur au nom de mon association',
                            ],
                        ]
                    ],
                ]
            ],
            'clients' => [
                'description' => 'Créer des évènements pour chaque client',
                'scopes' => [
                    'created' => [
                        'description' => 'Créer des évènements au nom d\'un client de chaque utilisateur',
                    ],
                    'owned' => [
                        'description' => 'Créer des évènements pour un client de chaque utilisateur',
                        'scopes' => [
                            'client' => [
                                'description' => 'Créer des évènements pour des applications de chaque utilisateur au nom de mon application',
                            ],
                            'asso' => [
                                'description' => 'Créer les évènements pour des applications de chaque utilisateur au nom de mon association',
                            ],
                        ]
                    ],
                ]
            ],
        ],
    ]
];
