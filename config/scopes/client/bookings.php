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

// All routes starting with client-{verbe}-bookings-
return [
    'description' => 'Réservations',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer les réservations',
            'scopes' => [
                'assos' => [
                    'description' => 'Gérer les réservations des associations des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Gérer les réservations que chaque association des utilisateurs ont créé',
                        ],
                        'owned' => [
                            'description' => 'Gérer les réservations que chaque association des utilisateurs possède',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Gérer les réservations que chaque association des utilisateurs possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Gérer les réservations que chaque association des utilisateurs possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Gérer les réservations que chaque association des utilisateurs possède et que mon association crée',
                                ],
                            ]
                        ],
                        'validated' => [
                            'description' => 'Gérer la validation des réservations de chaque association des utilisateurs',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Gérer la validation des utilisateurs des réservations de chaque association des utilisateurs',
                                ],
                                'client' => [
                                    'description' => 'Gérer la validation du client des réservations de chaque association des utilisateurs',
                                ],
                                'asso' => [
                                    'description' => 'Gérer la validation de l\'association des réservations de chaque association des utilisateurs',
                                ],
                            ]
                        ],
                    ]
                ],
                'users' => [
                    'description' => 'Gérer les réservations des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Gérer les réservations que les utilisateurs ont créé',
                        ],
                        'validated' => [
                            'description' => 'Gérer les réservations que les utilisateurs ont validé',
                        ],
                    ]
                ],
                'clients' => [
                    'description' => 'Gérer les réservations des clients des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Gérer les réservations des clients que les utilisateurs ont créé',
                        ],
                        'validated' => [
                            'description' => 'Gérer les réservations des clients que les utilisateurs ont validé',
                        ],
                    ]
                ],
            ]
        ],
        'set' => [
            'description' => 'Créer et modifier les réservations',
            'scopes' => [
                'assos' => [
                    'description' => 'Créer et modifier les réservations des associations des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Créer et modifier les réservations que chaque association des utilisateurs ont créé',
                        ],
                        'owned' => [
                            'description' => 'Créer et modifier les réservations que chaque association des utilisateurs possède',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Créer et modifier les réservations que chaque association des utilisateurs possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Créer et modifier les réservations que chaque association des utilisateurs possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Créer et modifier les réservations que chaque association des utilisateurs possède et que mon association crée',
                                ],
                            ]
                        ],
                        'validated' => [
                            'description' => 'Créer et modifier la validation des réservations de chaque association des utilisateurs',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Créer et modifier la validation des utilisateurs des réservations de chaque association des utilisateurs',
                                ],
                                'client' => [
                                    'description' => 'Créer et modifier la validation du client des réservations de chaque association des utilisateurs',
                                ],
                                'asso' => [
                                    'description' => 'Créer et modifier la validation de l\'association des réservations de chaque association des utilisateurs',
                                ],
                            ]
                        ],
                    ]
                ],
                'users' => [
                    'description' => 'Créer et modifier les réservations des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Créer et modifier les réservations que les utilisateurs ont créé',
                        ],
                        'validated' => [
                            'description' => 'Créer et modifier les réservations que les utilisateurs ont validé',
                        ],
                    ]
                ],
                'clients' => [
                    'description' => 'Créer et modifier les réservations des clients des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Créer et modifier les réservations des clients que les utilisateurs ont créé',
                        ],
                        'validated' => [
                            'description' => 'Créer et modifier les réservations des clients que les utilisateurs ont validé',
                        ],
                    ]
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer les réservations',
            'scopes' => [
                'assos' => [
                    'description' => 'Récupérer les réservations des associations des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Récupérer les réservations que chaque association des utilisateurs ont créé',
                        ],
                        'owned' => [
                            'description' => 'Récupérer les réservations que chaque association des utilisateurs possède',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Récupérer les réservations que chaque association des utilisateurs possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Récupérer les réservations que chaque association des utilisateurs possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Récupérer les réservations que chaque association des utilisateurs possède et que mon association crée',
                                ],
                            ]
                        ],
                        'validated' => [
                            'description' => 'Récupérer la validation des réservations de chaque association des utilisateurs',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Récupérer la validation des utilisateurs des réservations de chaque association des utilisateurs',
                                ],
                                'client' => [
                                    'description' => 'Récupérer la validation du client des réservations de chaque association des utilisateurs',
                                ],
                                'asso' => [
                                    'description' => 'Récupérer la validation de l\'association des réservations de chaque association des utilisateurs',
                                ],
                            ]
                        ],
                    ]
                ],
                'users' => [
                    'description' => 'Récupérer les réservations des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Récupérer les réservations que les utilisateurs ont créé',
                        ],
                        'validated' => [
                            'description' => 'Récupérer les réservations que les utilisateurs ont validé',
                        ],
                    ]
                ],
                'clients' => [
                    'description' => 'Récupérer les réservations des clients des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Récupérer les réservations des clients que les utilisateurs ont créé',
                        ],
                        'validated' => [
                            'description' => 'Récupérer les réservations des clients que les utilisateurs ont validé',
                        ],
                    ]
                ],
                'types' => [
                    'description' => 'Récupérer les types de réservation',
                ],
            ]
        ],
        'create' => [
            'description' => 'Créer les réservations',
            'scopes' => [
                'assos' => [
                    'description' => 'Créer les réservations des associations des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Créer les réservations que chaque association des utilisateurs ont créé',
                        ],
                        'owned' => [
                            'description' => 'Créer les réservations que chaque association des utilisateurs possède',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Créer les réservations que chaque association des utilisateurs possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Créer les réservations que chaque association des utilisateurs possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Créer les réservations que chaque association des utilisateurs possède et que mon association crée',
                                ],
                            ]
                        ],
                        'validated' => [
                            'description' => 'Créer la validation des réservations de chaque association des utilisateurs',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Créer la validation des utilisateurs des réservations de chaque association des utilisateurs',
                                ],
                                'client' => [
                                    'description' => 'Créer la validation du client des réservations de chaque association des utilisateurs',
                                ],
                                'asso' => [
                                    'description' => 'Créer la validation de l\'association des réservations de chaque association des utilisateurs',
                                ],
                            ]
                        ],
                    ]
                ],
                'users' => [
                    'description' => 'Créer les réservations des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Créer les réservations que les utilisateurs ont créé',
                        ],
                        'validated' => [
                            'description' => 'Créer les réservations que les utilisateurs ont validé',
                        ],
                    ]
                ],
                'clients' => [
                    'description' => 'Créer les réservations des clients des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Créer les réservations des clients que les utilisateurs ont créé',
                        ],
                        'validated' => [
                            'description' => 'Créer les réservations des clients que les utilisateurs ont validé',
                        ],
                    ]
                ],
            ]
        ],
        'edit' => [
            'description' => 'Modifier les réservations',
            'scopes' => [
                'assos' => [
                    'description' => 'Modifier les réservations des associations des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Modifier les réservations que chaque association des utilisateurs ont créé',
                        ],
                        'owned' => [
                            'description' => 'Modifier les réservations que chaque association des utilisateurs possède',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Modifier les réservations que chaque association des utilisateurs possède et qu\'il crée',
                                ],
                                'client' => [
                                    'description' => 'Modifier les réservations que chaque association des utilisateurs possède et que mon client crée',
                                ],
                                'asso' => [
                                    'description' => 'Modifier les réservations que chaque association des utilisateurs possède et que mon association crée',
                                ],
                            ]
                        ],
                        'validated' => [
                            'description' => 'Modifier la validation des réservations de chaque association des utilisateurs',
                            'scopes' => [
                                'user' => [
                                    'description' => 'Modifier la validation des utilisateurs des réservations de chaque association des utilisateurs',
                                ],
                                'client' => [
                                    'description' => 'Modifier la validation du client des réservations de chaque association des utilisateurs',
                                ],
                                'asso' => [
                                    'description' => 'Modifier la validation de l\'association des réservations de chaque association des utilisateurs',
                                ],
                            ]
                        ],
                    ]
                ],
                'users' => [
                    'description' => 'Modifier les réservations des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Modifier les réservations que les utilisateurs ont créé',
                        ],
                        'validated' => [
                            'description' => 'Modifier les réservations que les utilisateurs ont validé',
                        ],
                    ]
                ],
                'clients' => [
                    'description' => 'Modifier les réservations des clients des utilisateurs',
                    'scopes' => [
                        'created' => [
                            'description' => 'Modifier les réservations des clients que les utilisateurs ont créé',
                        ],
                        'validated' => [
                            'description' => 'Modifier les réservations des clients que les utilisateurs ont validé',
                        ],
                    ]
                ],
            ]
        ],
    ]
];
