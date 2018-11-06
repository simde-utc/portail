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

// Toutes les routes commencant par client-{verbe}-permissions-
return [
    'description' => 'Permissions',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes)',
            'scopes' => [
                'users' => [
                    'description' => 'Gérer les permissions et les assigner aux utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Gérer les permissions des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Gérer les permissions assignés aux utilisateurs',
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Gérer les permissions et les assigner aux associations des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Gérer les permissions des associations des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Gérer les permissions assignés aux associations des utilisateurs',
                        ],
                    ]
                ],
                'groups' => [
                    'description' => 'Gérer les permissions et les assigner aux groupes des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Gérer les permissions des groupes des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Gérer les permissions assignés aux groupes des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes)',
            'scopes' => [
                'users' => [
                    'description' => 'Récupérer les permissions et les assigner aux utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Récupérer les permissions des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Récupérer les permissions assignés aux utilisateurs',
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Récupérer les permissions et les assigner aux associations des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Récupérer les permissions des associations des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Récupérer les permissions assignés aux associations des utilisateurs',
                        ],
                    ]
                ],
                'groups' => [
                    'description' => 'Récupérer les permissions et les assigner aux groupes des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Récupérer les permissions des groupes des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Récupérer les permissions assignés aux groupes des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
        'set' => [
            'description' => 'Créer et modifier les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes)',
            'scopes' => [
                'users' => [
                    'description' => 'Créer et modifier les permissions et les assigner aux utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Créer et modifier les permissions des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Créer et modifier les permissions assignés aux utilisateurs',
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Créer et modifier les permissions et les assigner aux associations des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Créer et modifier les permissions des associations des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Créer et modifier les permissions assignés aux associations des utilisateurs',
                        ],
                    ]
                ],
                'groups' => [
                    'description' => 'Créer et modifier les permissions et les assigner aux groupes des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Créer et modifier les permissions des groupes des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Créer et modifier les permissions assignés aux groupes des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
        'edit' => [
            'description' => 'Modifier les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes)',
            'scopes' => [
                'users' => [
                    'description' => 'Modifier les permissions et les assigner aux utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Modifier les permissions des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Modifier les permissions assignés aux utilisateurs',
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Modifier les permissions et les assigner aux associations des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Modifier les permissions des associations des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Modifier les permissions assignés aux associations des utilisateurs',
                        ],
                    ]
                ],
                'groups' => [
                    'description' => 'Modifier les permissions et les assigner aux groupes des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Modifier les permissions des groupes des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Modifier les permissions assignés aux groupes des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
        'create' => [
            'description' => 'Créer les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes)',
            'scopes' => [
                'users' => [
                    'description' => 'Créer les permissions et les assigner aux utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Créer les permissions des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Créer les permissions assignés aux utilisateurs',
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Créer les permissions et les assigner aux associations des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Créer les permissions des associations des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Créer les permissions assignés aux associations des utilisateurs',
                        ],
                    ]
                ],
                'groups' => [
                    'description' => 'Créer les permissions et les assigner aux groupes des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Créer les permissions des groupes des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Créer les permissions assignés aux groupes des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
        'remove' => [
            'description' => 'Supprimer les permissions et les assigner (permissions systèmes, permissions associations, permissions groupes)',
            'scopes' => [
                'users' => [
                    'description' => 'Supprimer les permissions et les assigner aux utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Supprimer les permissions des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Supprimer les permissions assignés aux utilisateurs',
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Supprimer les permissions et les assigner aux associations des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Supprimer les permissions des associations des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Supprimer les permissions assignés aux associations des utilisateurs',
                        ],
                    ]
                ],
                'groups' => [
                    'description' => 'Supprimer les permissions et les assigner aux groupes des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Supprimer les permissions des groupes des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Supprimer les permissions assignés aux groupes des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
    ]
];
