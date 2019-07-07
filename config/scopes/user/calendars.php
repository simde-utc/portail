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

// Toutes les routes commencant par user-{verbe}-calendar-
return [
    'description' => 'Calendriers',
    'icon' => 'calendar-alt',
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
                                'user' => [
                                    'description' => 'Gérer les calendriers que l\'utilisateur possède et crée',
                                ],
                                'client' => [
                                    'description' => 'Gérer les calendriers que l\'utilisateur possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Gérer les calendriers que l\'utilisateur possède et que mon association crée',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Gérer les calendriers que l\'utilisateur suit',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Gérer les calendriers utilisateurs que l\'utilisateur suit',
                                ],
                                'asso' => [
                                    'description' => 'Gérer les calendriers associatifs que l\'utilisateur suit',
                                ],
                                'group' => [
                                    'description' => 'Gérer les calendriers de groupe que l\'utilisateur suit',
                                ],
                                'client' => [
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
                                'user' => [
                                    'description' => 'Gérer les calendriers que chaque association de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Gérer les calendriers que chaque association de l\'utilisateur possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Gérer les calendriers que chaque association de l\'utilisateur possède et que mon association crée',
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
                                'user' => [
                                    'description' => 'Gérer les calendriers que chaque groupe de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Gérer les calendriers que chaque groupe possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Gérer les calendriers que chaque association de l\'utilisateur possède et que mon association crée',
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
                                'user' => [
                                    'description' => 'Gérer les calendriers que chaque client de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Gérer les calendriers que chaque client possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Gérer les calendriers que chaque client de l\'utilisateur possède et que mon association crée',
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
                                'user' => [
                                    'description' => 'Récupérer les calendriers que l\'utilisateur possède et crée',
                                ],
                                'client' => [
                                    'description' => 'Récupérer les calendriers que l\'utilisateur possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Récupérer les calendriers que l\'utilisateur possède et que mon association crée',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Récupérer les calendriers que l\'utilisateur suit',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Récupérer les calendriers utilisateurs que l\'utilisateur suit',
                                ],
                                'asso' => [
                                    'description' => 'Récupérer les calendriers associatifs que l\'utilisateur suit',
                                ],
                                'group' => [
                                    'description' => 'Récupérer les calendriers de groupe que l\'utilisateur suit',
                                ],
                                'client' => [
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
                                'user' => [
                                    'description' => 'Récupérer les calendriers que chaque association de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Récupérer les calendriers que chaque association possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Récupérer les calendriers que chaque association de l\'utilisateur possède et que mon association crée',
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
                                'user' => [
                                    'description' => 'Récupérer les calendriers que chaque groupe de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Récupérer les calendriers que chaque groupe possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Récupérer les calendriers que chaque groupe de l\'utilisateur possède et que mon association crée',
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
                                'user' => [
                                    'description' => 'Récupérer les calendriers que chaque client de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Récupérer les calendriers que chaque client possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Récupérer les calendriers que chaque client de l\'utilisateur possède et que mon association crée',
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
                                'user' => [
                                    'description' => 'Modifier et créer les calendriers que l\'utilisateur possède et crée',
                                ],
                                'client' => [
                                    'description' => 'Modifier et créer les calendriers que l\'utilisateur possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Modifier et créer les calendriers que l\'utilisateur possède et que mon association crée',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Modifier et créer les calendriers que l\'utilisateur suit',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Modifier et créer les calendriers utilisateurs que l\'utilisateur suit',
                                ],
                                'asso' => [
                                    'description' => 'Modifier et créer les calendriers associatifs que l\'utilisateur suit',
                                ],
                                'group' => [
                                    'description' => 'Modifier et créer les calendriers de groupe que l\'utilisateur suit',
                                ],
                                'client' => [
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
                                'user' => [
                                    'description' => 'Modifier et créer les calendriers que chaque association de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Modifier et créer les calendriers que chaque association possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Modifier et créer les calendriers que chaque association de l\'utilisateur possède et que mon association crée',
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
                                'user' => [
                                    'description' => 'Modifier et créer les calendriers que chaque groupe de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Modifier et créer les calendriers que chaque groupe possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Modifier et créer les calendriers que chaque groupe de l\'utilisateur possède et que mon association crée',
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
                                'user' => [
                                    'description' => 'Modifier et créer les calendriers que chaque client de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Modifier et créer les calendriers que chaque client possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Modifier et créer les calendriers que chaque client de l\'utilisateur possède et que mon association crée',
                                ],
                            ]
                        ],
                    ]
                ],
            ]
        ],
        'edit' => [
            'description' => 'Modifier les calendriers',
            'scopes' => [
                'users' => [
                    'description' => 'Modifier les calendriers de l\'utilisateur',
                    'scopes' => [
                        'created' => [
                            'description' => 'Modifier les calendriers que l\'utilisateur a créé',
                        ],
                        'owned' => [
                            'description' => 'Modifier les calendriers que l\'utilisateur possède',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Modifier les calendriers que l\'utilisateur possède et crée',
                                ],
                                'client' => [
                                    'description' => 'Modifier les calendriers que l\'utilisateur possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Modifier les calendriers que l\'utilisateur possède et que mon association crée',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Modifier les calendriers que l\'utilisateur suit',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Modifier les calendriers utilisateurs que l\'utilisateur suit',
                                ],
                                'asso' => [
                                    'description' => 'Modifier les calendriers associatifs que l\'utilisateur suit',
                                ],
                                'group' => [
                                    'description' => 'Modifier les calendriers de groupe que l\'utilisateur suit',
                                ],
                                'client' => [
                                    'description' => 'Modifier les calendriers clients que l\'utilisateur suit',
                                ],
                            ]
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Modifier les calendriers de chaque association',
                    'scopes' => [
                        'created' => [
                            'description' => 'Modifier les calendriers que chaque association a créé',
                        ],
                        'owned' => [
                            'description' => 'Modifier les calendriers que chaque association possède',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Modifier les calendriers que chaque association de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Modifier les calendriers que chaque association possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Modifier les calendriers que chaque association de l\'utilisateur possède et que mon association crée',
                                ],
                            ]
                        ],
                    ]
                ],
                'groups' => [
                    'description' => 'Modifier les calendriers de chaque groupe',
                    'scopes' => [
                        'created' => [
                            'description' => 'Modifier les calendriers que chaque groupe a créé',
                        ],
                        'owned' => [
                            'description' => 'Modifier les calendriers que chaque groupe possède',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Modifier les calendriers que chaque groupe de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Modifier les calendriers que chaque groupe possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Modifier les calendriers que chaque groupe de l\'utilisateur possède et que mon association crée',
                                ],
                            ]
                        ],
                    ]
                ],
                'clients' => [
                    'description' => 'Modifier les calendriers de chaque client',
                    'scopes' => [
                        'created' => [
                            'description' => 'Modifier les calendriers que chaque client a créé',
                        ],
                        'owned' => [
                            'description' => 'Modifier les calendriers que chaque client possède',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Modifier les calendriers que chaque client de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Modifier les calendriers que chaque client possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Modifier les calendriers que chaque client de l\'utilisateur possède et que mon association crée',
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
                                'user' => [
                                    'description' => 'Créer les calendriers que l\'utilisateur possède et crée',
                                ],
                                'client' => [
                                    'description' => 'Créer des calendriers pour l\'utilisateur au nom de mon application',
                                ],
                                'asso' => [
                                    'description' => 'Créer les calendriers pour l\'utilisateur possède et que mon association crée',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Faire suivre des calendriers à l\'utilisateur',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Faire suivre des calendriers utilisateurs à l\'utilisateur',
                                ],
                                'asso' => [
                                    'description' => 'Faire suivre des calendriers associatifs à l\'utilisateur',
                                ],
                                'group' => [
                                    'description' => 'Faire suivre des calendriers de groupe à l\'utilisateur',
                                ],
                                'client' => [
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
                                'user' => [
                                    'description' => 'Créer des calendriers que chaque association de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Créer des calendriers pour des associations de l\'utilisateur au nom de mon application',
                                ],
                                'asso' => [
                                    'description' => 'Créer les calendriers pour chaque association de l\'utilisateur possède et que mon association crée',
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
                                'user' => [
                                    'description' => 'Créer des calendriers que chaque groupe de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Créer des calendriers pour des groupes de l\'utilisateur au nom de mon application',
                                ],
                                'asso' => [
                                    'description' => 'Créer les calendriers pour chaque groupe de l\'utilisateur possède et que mon association crée',
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
                                'user' => [
                                    'description' => 'Créer des calendriers que chaque application de l\'utilisateur possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Créer des calendriers pour des applications de l\'utilisateur au nom de mon application',
                                ],
                                'asso' => [
                                    'description' => 'Créer les calendriers pour chaque application de l\'utilisateur possède et que mon association crée',
                                ],
                            ]
                        ],
                    ]
                ],
            ]
        ],
    ]
];
