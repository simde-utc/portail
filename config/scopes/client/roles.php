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

// Toutes les routes commencant par client-{verbe}-roles-
return [
    'description' => 'Roles',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer les rôles et les assigner (rôles systèmes, rôles associations, rôles groupes)',
            'scopes' => [
                'users' => [
                    'description' => 'Gérer les rôles et les assigner aux utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Gérer les rôles des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Gérer les rôles assignés aux utilisateurs',
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Gérer les rôles et les assigner aux associations des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Gérer les rôles des associations des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Gérer les rôles assignés aux associations des utilisateurs',
                        ],
                    ]
                ],
                'groups' => [
                    'description' => 'Gérer les rôles et les assigner aux groupes des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Gérer les rôles des groupes des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Gérer les rôles assignés aux groupes des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer les rôles et les assigner (rôles systèmes, rôles associations, rôles groupes)',
            'scopes' => [
                'users' => [
                    'description' => 'Récupérer les rôles et les assigner aux utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Récupérer les rôles des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Récupérer les rôles assignés aux utilisateurs',
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Récupérer les rôles et les assigner aux associations des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Récupérer les rôles des associations des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Récupérer les rôles assignés aux associations des utilisateurs',
                        ],
                    ]
                ],
                'groups' => [
                    'description' => 'Récupérer les rôles et les assigner aux groupes des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Récupérer les rôles des groupes des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Récupérer les rôles assignés aux groupes des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
        'set' => [
            'description' => 'Créer et modifier les rôles et les assigner (rôles systèmes, rôles associations, rôles groupes)',
            'scopes' => [
                'users' => [
                    'description' => 'Créer et modifier les rôles et les assigner aux utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Créer et modifier les rôles des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Créer et modifier les rôles assignés aux utilisateurs',
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Créer et modifier les rôles et les assigner aux associations des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Créer et modifier les rôles des associations des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Créer et modifier les rôles assignés aux associations des utilisateurs',
                        ],
                    ]
                ],
                'groups' => [
                    'description' => 'Créer et modifier les rôles et les assigner aux groupes des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Créer et modifier les rôles des groupes des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Créer et modifier les rôles assignés aux groupes des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
        'edit' => [
            'description' => 'Modifier les rôles et les assigner (rôles systèmes, rôles associations, rôles groupes)',
            'scopes' => [
                'users' => [
                    'description' => 'Modifier les rôles et les assigner aux utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Modifier les rôles des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Modifier les rôles assignés aux utilisateurs',
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Modifier les rôles et les assigner aux associations des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Modifier les rôles des associations des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Modifier les rôles assignés aux associations des utilisateurs',
                        ],
                    ]
                ],
                'groups' => [
                    'description' => 'Modifier les rôles et les assigner aux groupes des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Modifier les rôles des groupes des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Modifier les rôles assignés aux groupes des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
        'create' => [
            'description' => 'Créer les rôles et les assigner (rôles systèmes, rôles associations, rôles groupes)',
            'scopes' => [
                'users' => [
                    'description' => 'Créer les rôles et les assigner aux utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Créer les rôles des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Créer les rôles assignés aux utilisateurs',
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Créer les rôles et les assigner aux associations des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Créer les rôles des associations des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Créer les rôles assignés aux associations des utilisateurs',
                        ],
                    ]
                ],
                'groups' => [
                    'description' => 'Créer les rôles et les assigner aux groupes des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Créer les rôles des groupes des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Créer les rôles assignés aux groupes des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
        'remove' => [
            'description' => 'Supprimer les rôles et les assigner (rôles systèmes, rôles associations, rôles groupes)',
            'scopes' => [
                'users' => [
                    'description' => 'Supprimer les rôles et les assigner aux utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Supprimer les rôles des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Supprimer les rôles assignés aux utilisateurs',
                        ],
                    ]
                ],
                'assos' => [
                    'description' => 'Supprimer les rôles et les assigner aux associations des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Supprimer les rôles des associations des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Supprimer les rôles assignés aux associations des utilisateurs',
                        ],
                    ]
                ],
                'groups' => [
                    'description' => 'Supprimer les rôles et les assigner aux groupes des utilisateurs',
                    'scopes' => [
                        'owned' => [
                            'description' => 'Supprimer les rôles des groupes des utilisateurs',
                        ],
                        'assigned' => [
                            'description' => 'Supprimer les rôles assignés aux groupes des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
    ]
];
