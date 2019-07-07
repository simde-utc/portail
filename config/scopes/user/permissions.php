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
 *         + edit:    Update data
 *       + remove:  Delete data
 */

// All routes starting with user-{verbe}-permissions-
return [
    'description' => 'Permissions',
    'icon' => 'gavel',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes)',
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
            ]
        ],
        'get' => [
            'description' => 'Récupérer les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes)',
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
            ]
        ],
        'set' => [
            'description' => 'Créer et modifier les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes)',
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
            ]
        ],
        'edit' => [
            'description' => 'Modifier les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes)',
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
            ]
        ],
        'create' => [
            'description' => 'Créer les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes)',
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
            ]
        ],
        'remove' => [
            'description' => 'Supprimer les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes)',
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
            ]
        ],
    ]
];
