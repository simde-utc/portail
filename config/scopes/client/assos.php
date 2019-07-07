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

// All routes starting with client-{verbe}-assos-
return [
    'description' => 'Associations',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer les associations et leurs membres',
            'scopes' => [
                'members' => [
                    'description' => 'Gérer les associations que les utilisateurs suient ou font',
                    'scopes' => [
                        'joined' => [
                            'description' => 'Gérer les associations que les utilisateurs font',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Gérer les associations que les utilisateurs font durant l\'actuel semestre',
                                ],
                            ]
                        ],
                        'joining' => [
                            'description' => 'Gérer les associations que les utilisateurs souhaitent rejoindre',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Gérer les associations que les utilisateurs souhaitent rejoindre durant l\'actuel semestre',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Gérer les associations que les utilisateurs suient',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Gérer les associations que les utilisateurs suient durant l\'actuel semestre',
                                ],
                            ]
                        ],
                    ]
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer les associations et leurs membres',
            'scopes' => [
                'members' => [
                    'description' => 'Récupérer toutes les associations que les utilisateurs suient ou font',
                    'scopes' => [
                        'joined' => [
                            'description' => 'Récupérer toutes les associations que les utilisateurs font',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Récupérer toutes les associations que les utilisateurs font durant l\'actuel semestre',
                                ],
                            ]
                        ],
                        'joining' => [
                            'description' => 'Récupérer toutes les associations que les utilisateurs souhaitent rejoindre',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Récupérer toutes les associations que les utilisateurs souhaitent rejoindre durant l\'actuel semestre',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Récupérer toutes les associations que les utilisateurs suient',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Récupérer toutes les associations que les utilisateurs suient durant l\'actuel semestre',
                                ],
                            ]
                        ],
                        'members' => [
                            'description' => 'Récupérer tous les membres des associations que les utilisateurs suient ou font',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Récupérer tous les membres des associations que les utilisateurs suient ou font durant l\'actuel semestre',
                                ],
                            ]
                        ],
                    ]
                ],
            ]
        ],
        'set' => [
            'description' => 'Ajouter et modifier les associations et leurs membres',
            'scopes' => [
                'members' => [
                    'description' => 'Ajouter et modifier les associations que les utilisateurs suient ou font',
                    'scopes' => [
                        'joined' => [
                            'description' => 'Ajouter et modifier les associations que les utilisateurs font',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Ajouter et modifier les associations que les utilisateurs font durant l\'actuel semestre',
                                ],
                            ]
                        ],
                        'joining' => [
                            'description' => 'Ajouter et modifier les associations que les utilisateurs souhaitent rejoindre',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Ajouter et modifier les associations que les utilisateurs souhaitent rejoindre durant l\'actuel semestre',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Ajouter et modifier les associations que les utilisateurs suient',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Ajouter et modifier les associations que les utilisateurs suient durant l\'actuel semestre',
                                ],
                            ]
                        ],
                    ]
                ],
            ]
        ],
        'edit' => [
            'description' => 'Modifier les associations et leurs membres',
            'scopes' => [
                'members' => [
                    'description' => 'Modifier les associations que les utilisateurs suient ou font',
                    'scopes' => [
                        'joined' => [
                            'description' => 'Modifier les associations que les utilisateurs font',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Modifier les associations que les utilisateurs font durant l\'actuel semestre',
                                ],
                            ]
                        ],
                        'joining' => [
                            'description' => 'Modifier les associations que les utilisateurs souhaitent rejoindre',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Modifier les associations que les utilisateurs souhaitent rejoindre durant l\'actuel semestre',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Modifier les associations que les utilisateurs suient',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Modifier les associations que les utilisateurs suient durant l\'actuel semestre',
                                ],
                            ]
                        ],
                    ]
                ],
            ]
        ],
        'create' => [
            'description' => 'Créer des associations et ajouter des membres aux associations',
            'scopes' => [
                'members' => [
                    'description' => 'Ajouter des associations que les utilisateurs suient ou font',
                    'scopes' => [
                        'joined' => [
                            'description' => 'Ajouter des associations que les utilisateurs font',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Ajouter des associations que les utilisateurs font durant l\'actuel semestre',
                                ],
                            ]
                        ],
                        'joining' => [
                            'description' => 'Ajouter des associations que les utilisateurs souhaitent rejoindre',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Ajouter des associations que les utilisateurs souhaitent rejoindre durant l\'actuel semestre',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Ajouter des associations que les utilisateurs suient',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Ajouter des associations que les utilisateurs suient durant l\'actuel semestre',
                                ],
                            ]
                        ],
                    ]
                ],
            ]
        ],
        'remove' => [
            'description' => 'Supprimer des associations et retirer des membres aux associations',
            'scopes' => [
                'members' => [
                    'description' => 'Retirer des associations que les utilisateurs suient ou font',
                    'scopes' => [
                        'joined' => [
                            'description' => 'Retirer des associations que les utilisateurs font',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Retirer des associations que les utilisateurs font durant l\'actuel semestre',
                                ],
                            ]
                        ],
                        'joining' => [
                            'description' => 'Retirer des associations que les utilisateurs souhaitent rejoindre',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Retirer des associations que les utilisateurs souhaitent rejoindre durant l\'actuel semestre',
                                ],
                            ]
                        ],
                        'followed' => [
                            'description' => 'Retirer des associations que les utilisateurs suient',
                            'scopes' => [
                                'now' => [
                                    'description' => 'Retirer des associations que les utilisateurs suient durant l\'actuel semestre',
                                ],
                            ]
                        ],
                    ]
                ],
            ]
        ],
    ]
];
