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

// All routes starting with client-{verbe}-info-
return [
    'description' => 'Informations personnelles',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer totalement les informations des utilisateurs',
            'scopes' => [
                'identity' => [
                    'description' => 'Gérer l\'identité des utilisateurs',
                    'scopes' => [
                        'email' => [
                            'description' => 'Gérer l\'adresse email des utilisateurs',
                        ],
                        'names' => [
                            'description' => 'Gérer les nom et prénom des utilisateurs',
                        ],
                        'auth' => [
                            'description' => 'Gérer les types de connexions des utilisateurs',
                            'scopes' => [
                                'cas' => [
                                    'description' => 'Gérer les données CAS des utilisateurs',
                                ],
                                'password' => [
                                    'description' => 'Gérer les données liées à la création du compte mot de passe des utilisateurs',
                                ],
                                'app' => [
                                    'description' => 'Gérer les données liées à la création des comptes applications des utilisateurs',
                                ],
                            ]
                        ],
                    ]
                ],
                'details' => [
                    'description' => 'Gérer tous les détails des utilisateurs (date de naissance, âge, majorité, login CAS-UTC, cotisation BDE)',
                    'scopes' => [
                        'birthdate' => [
                            'description' => 'Gérer la date de naisssance des utilisateurs',
                        ],
                    ]
                ],
                'preferences' => [
                    'description' => 'Gérer toutes les préférences des utilisateurs (globales, de l\'association, du client)',
                    'scopes' => [
                        'global' => [
                            'description' => 'Gérer les préférences globales des utilisateurs',
                        ],
                        'asso' => [
                            'description' => 'Gérer les préférences de l\'association des utilisateurs',
                        ],
                        'client' => [
                            'description' => 'Gérer les préférences du client des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
        'set' => [
            'description' => "Modifier et ajouter des informations des utilisateurs",
            'scopes' => [
                'identity' => [
                    'description' => 'Modifier et ajouter des informations concernant l\'identité des utilisateurs',
                    'scopes' => [
                        'email' => [
                            'description' => 'Modifier l\'adresse email des utilisateurs',
                        ],
                        'names' => [
                            'description' => 'Modifier les nom et prénom des utilisateurs',
                        ],
                        'auth' => [
                            'description' => 'Modifier et ajouter des types de connexions des utilisateurs',
                            'scopes' => [
                                'cas' => [
                                    'description' => 'Modifier et ajouter des données CAS des utilisateurs',
                                ],
                                'password' => [
                                    'description' => 'Modifier et ajouter des données liées à la création du compte mot de passe des utilisateurs',
                                ],
                                'app' => [
                                    'description' => 'Modifier et ajouter des données liées à la création des comptes applications des utilisateurs',
                                ],
                            ]
                        ],
                    ]
                ],
                'details' => [
                    'description' => 'Modifier tous les détails des utilisateurs (date de naissance, âge, majorité)',
                    'scopes' => [
                        'birthdate' => [
                            'description' => 'Modifier la date de naisssance des utilisateurs',
                        ],
                    ]
                ],
                'preferences' => [
                    'description' => 'Modifier et ajouter des préférences pour l\'utilisateur (globales, de l\'association, du client)',
                    'scopes' => [
                        'global' => [
                            'description' => 'Modifier et des ajouter des préférences globales pour l\'utilisateur',
                        ],
                        'asso' => [
                            'description' => 'Modifier et des ajouter des préférences de l\'association pour l\'utilisateur',
                        ],
                        'client' => [
                            'description' => 'Modifier et des ajouter des préférences du client pour l\'utilisateur',
                        ],
                    ]
                ],
            ]
        ],
        'get' => [
            'description' => 'Récupérer toutes les informations des utilisateurs',
            'scopes' => [
                'identity' => [
                    'description' => 'Récupérer l\'identité des utilisateurs',
                    'scopes' => [
                        'email' => [
                            'description' => 'Récupérer l\'adresse email des utilisateurs',
                        ],
                        'timestamps' => [
                            'description' => 'Connaître les moments de connexion et de création des utilisateurs',
                        ],
                        'type' => [
                            'description' => 'Connaître le type des utilisateurs',
                            'scopes' => [
                                'active' => [
                                    'description' => 'Savoir si le compte est actif',
                                ],
                                'password' => [
                                    'description' => 'Savoir si l\'utilisateur se connecte via email/mot de passe',
                                ],
                                'cas' => [
                                    'description' => 'Savoir si l\'utilisateur est un utilisateur CAS-UTC',
                                ],
                                'casConfirmed' => [
                                    'description' => 'Savoir si l\'utilisateur est un utilisateur CAS-UTC et qu\'il peut s\'y connecter',
                                ],
                                'contributorBde' => [
                                    'description' => 'Savoir si l\'utilisateur est un cotisant BDE-UTC',
                                ],
                                'admin' => [
                                    'description' => 'Savoir si l\'utilisateur est un administrateur du système',
                                ],
                                'member' => [
                                    'description' => 'Savoir si l\'utilisateur est membre d\'une association',
                                ],
                            ]
                        ],
                        'auth' => [
                            'description' => 'Connaître les types de connexions des utilisateurs',
                            'scopes' => [
                                'cas' => [
                                    'description' => 'Avoir les données CAS des utilisateurs',
                                ],
                                'password' => [
                                    'description' => 'Avoir les données liées à la création du compte mot de passe des utilisateurs',
                                ],
                                'app' => [
                                    'description' => 'Avoir les données liées à la création des comptes applications des utilisateurs',
                                ],
                            ]
                        ],
                    ]
                ],
                'details' => [
                    'description' => 'Connaître tous les détails des utilisateurs (date de naissance, âge, majorité, login CAS-UTC, cotisation BDE)',
                    'scopes' => [
                        'birthdate' => [
                            'description' => 'Connaître la date de naisssance des utilisateurs',
                        ],
                        'age' => [
                            'description' => 'Connaître l\'âge des utilisateurs',
                        ],
                        'ismajor' => [
                            'description' => 'Connaître si l\'utilisateur est majeur ou non',
                        ],
                        'logincas' => [
                            'description' => 'Connaître le login CAS-UTC des utilisateurs s\'il en possède un',
                        ],
                        'logincontributorbde' => [
                            'description' => 'Connaître le login cotisant BDE des utilisateurs s\'il en possède un',
                        ],
                        'isContributorBde' => [
                            'description' => 'Connaître si l\'utilisateur est cotisant BDE ou non',
                        ]
                    ]
                ],
                'preferences' => [
                    'description' => 'Connaître toutes les préférences des utilisateurs (globales, de l\'association, du client)',
                    'scopes' => [
                        'global' => [
                            'description' => 'Connaître les préférences globales des utilisateurs',
                        ],
                        'asso' => [
                            'description' => 'Connaître les préférences de l\'association des utilisateurs',
                        ],
                        'client' => [
                            'description' => 'Connaître les préférences du client des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
        'edit' => [
            'description' => "Modifier toutes les informations des utilisateurs",
            'scopes' => [
                'identity' => [
                    'description' => 'Modifier l\'identité des utilisateurs',
                    'scopes' => [
                        'email' => [
                            'description' => 'Modifier l\'adresse email des utilisateurs',
                        ],
                        'names' => [
                            'description' => 'Modifier les nom et prénom des utilisateurs',
                        ],
                        'auth' => [
                            'description' => 'Modifier les types de connexions des utilisateurs',
                            'scopes' => [
                                'cas' => [
                                    'description' => 'Modifier les données CAS des utilisateurs',
                                ],
                                'password' => [
                                    'description' => 'Modifier les données liées à la création du compte mot de passe des utilisateurs',
                                ],
                                'app' => [
                                    'description' => 'Modifier les données liées à la création des comptes applications des utilisateurs',
                                ],
                            ]
                        ],
                    ]
                ],
                'details' => [
                    'description' => 'Modifier tous les détails des utilisateurs (date de naissance, âge, majorité)',
                    'scopes' => [
                        'birthdate' => [
                            'description' => 'Modifier la date de naisssance des utilisateurs',
                        ],
                    ]
                ],
                'preferences' => [
                    'description' => 'Modifier toutes les préférences des utilisateurs (globales, de l\'association, du client)',
                    'scopes' => [
                        'global' => [
                            'description' => 'Modifier les préférences globales des utilisateurs',
                        ],
                        'asso' => [
                            'description' => 'Modifier les préférences de l\'association des utilisateurs',
                        ],
                        'client' => [
                            'description' => 'Modifier les préférences du client des utilisateurs',
                        ],
                    ]
                ],
            ]
        ],
        'create' => [
            'description' => "Ajouter des informations pour l'utilisateur",
            'scopes' => [
                'identity' => [
                    'description' => 'Ajouter des informations concernant l\'identité des utilisateurs',
                    'scopes' => [
                        'auth' => [
                            'description' => 'Ajouter des types de connexions des utilisateurs',
                            'scopes' => [
                                'cas' => [
                                    'description' => 'Ajouter des données CAS des utilisateurs',
                                ],
                                'password' => [
                                    'description' => 'Ajouter des données liées à la création du compte mot de passe des utilisateurs',
                                ],
                                'app' => [
                                    'description' => 'Ajouter des données liées à la création des comptes applications des utilisateurs',
                                ],
                            ]
                        ],
                    ]
                ],
                'preferences' => [
                    'description' => 'Ajouter des préférences pour l\'utilisateur (globales, de l\'association, du client)',
                    'scopes' => [
                        'global' => [
                            'description' => 'Ajouter des préférences globales pour l\'utilisateur',
                        ],
                        'asso' => [
                            'description' => 'Ajouter deses préférences de l\'association pour l\'utilisateur',
                        ],
                        'client' => [
                            'description' => 'Ajouter deses préférences du client pour l\'utilisateur',
                        ],
                    ]
                ],
                'details' => [
                    'description' => 'Modifier tous les détails des utilisateurs (date de naissance, âge, majorité)',
                    'scopes' => [
                        'birthdate' => [
                            'description' => 'Modifier la date de naisssance des utilisateurs',
                        ],
                        'age' => [
                            'description' => 'Modifier l\'âge des utilisateurs',
                        ]
                    ]
                ],
            ]
        ],
    ]
];
