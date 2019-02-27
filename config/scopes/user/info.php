<?php
/**
 * Liste des scopes en fonction des routes
 *   - Définition des scopes:
 *      portée + '-' + verbe + '-' + categorie + (pour chaque sous-catégorie: '-' + sous-catégorie)
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

// Toutes les routes commencant par user-{verbe}-info-
return [
    'description' => 'Informations personnelles',
    'icon' => 'user-circle',
    'verbs' => [
        'manage' => [
            'description' => 'Gérer totalement les informations de l\'utilisateur',
            'scopes' => [
                'identity' => [
                    'description' => 'Gérer l\'identité de l\'utilisateur',
                    'scopes' => [
                        'email' => [
                            'description' => 'Gérer l\'adresse email de l\'utilisateur',
                        ],
                        'names' => [
                            'description' => 'Gérer les nom et prénom de l\'utilisateur',
                        ],
                        'auth' => [
                            'description' => 'Gérer les types de connexions de l\'utilisateur',
                            'scopes' => [
                                'cas' => [
                                    'description' => 'Gérer les données CAS de l\'utilisateur',
                                ],
                                'password' => [
                                    'description' => 'Gérer les données liées à la création du compte mot de passe de l\'utilisateur',
                                ],
                                'app' => [
                                    'description' => 'Gérer les données liées à la création des comptes applications de l\'utilisateur',
                                ],
                            ]
                        ],
                    ]
                ],
                'details' => [
                    'description' => 'Gérer tous les détails de l\'utilisateur (date de naissance, âge, majorité, login CAS-UTC, cotisation BDE)',
                    'scopes' => [
                        'birthdate' => [
                            'description' => 'Gérer la date de naisssance de l\'utilisateur',
                        ],
                    ]
                ],
                'preferences' => [
                    'description' => 'Gérer toutes les préférences de l\'utilisateur (globales, de l\'association, du client)',
                    'scopes' => [
                        'global' => [
                            'description' => 'Gérer les préférences globales de l\'utilisateur',
                        ],
                        'asso' => [
                            'description' => 'Gérer les préférences de l\'association de l\'utilisateur',
                        ],
                        'client' => [
                            'description' => 'Gérer les préférences du client de l\'utilisateur',
                        ],
                    ]
                ],
            ]
        ],
        'set' => [
            'description' => 'Modifier et ajouter des informations de l\'utilisateur',
            'scopes' => [
                'identity' => [
                    'description' => 'Modifier et ajouter des informations concernant l\'identité de l\'utilisateur',
                    'scopes' => [
                        'email' => [
                            'description' => 'Modifier l\'adresse email de l\'utilisateur',
                        ],
                        'names' => [
                            'description' => 'Modifier les nom et prénom de l\'utilisateur',
                        ],
                        'auth' => [
                            'description' => 'Modifier et ajouter des types de connexions de l\'utilisateur',
                            'scopes' => [
                                'cas' => [
                                    'description' => 'Modifier et ajouter des données CAS de l\'utilisateur',
                                ],
                                'password' => [
                                    'description' => 'Modifier et ajouter des données liées à la création du compte mot de passe de l\'utilisateur',
                                ],
                                'app' => [
                                    'description' => 'Modifier et ajouter des données liées à la création des comptes applications de l\'utilisateur',
                                ],
                            ]
                        ],
                    ]
                ],
                'details' => [
                    'description' => 'Modifier tous les détails de l\'utilisateur (date de naissance, âge, majorité)',
                    'scopes' => [
                        'birthdate' => [
                            'description' => 'Modifier la date de naisssance de l\'utilisateur',
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
            'description' => 'Récupérer toutes les informations de l\'utilisateur',
            'scopes' => [
                'identity' => [
                    'description' => 'Récupérer l\'identité de l\'utilisateur',
                    'scopes' => [
                        'email' => [
                            'description' => 'Récupérer l\'adresse email de l\'utilisateur',
                        ],
                        'timestamps' => [
                            'description' => 'Connaître les moments de connexion et de création de l\'utilisateur',
                        ],
                        'type' => [
                            'description' => 'Connaître le type de l\'utilisateur',
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
                            'description' => 'Connaître les types de connexions de l\'utilisateur',
                            'scopes' => [
                                'cas' => [
                                    'description' => 'Avoir les données CAS de l\'utilisateur',
                                ],
                                'password' => [
                                    'description' => 'Avoir les données liées à la création du compte mot de passe de l\'utilisateur',
                                ],
                                'app' => [
                                    'description' => 'Avoir les données liées à la création des comptes applications de l\'utilisateur',
                                ],
                            ]
                        ],
                    ]
                ],
                'details' => [
                    'description' => 'Connaître tous les détails de l\'utilisateur (date de naissance, âge, majorité, login CAS-UTC, cotisation BDE)',
                    'scopes' => [
                        'birthdate' => [
                            'description' => 'Connaître la date de naisssance de l\'utilisateur',
                        ],
                        'age' => [
                            'description' => 'Connaître l\'âge de l\'utilisateur',
                        ],
                        'ismajor' => [
                            'description' => 'Connaître si l\'utilisateur est majeur ou non',
                        ],
                        'logincas' => [
                            'description' => 'Connaître le login CAS-UTC de l\'utilisateur s\'il en possède un',
                        ],
                        'logincontributorbde' => [
                            'description' => 'Connaître le login cotisant BDE de l\'utilisateur s\'il en possède un',
                        ],
                        'isContributorBde' => [
                            'description' => 'Connaître si l\'utilisateur est cotisant BDE ou non',
                        ]
                    ]
                ],
                'preferences' => [
                    'description' => 'Connaître toutes les préférences de l\'utilisateur (globales, de l\'association, du client)',
                    'scopes' => [
                        'global' => [
                            'description' => 'Connaître les préférences globales de l\'utilisateur',
                        ],
                        'asso' => [
                            'description' => 'Connaître les préférences de l\'association de l\'utilisateur',
                        ],
                        'client' => [
                            'description' => 'Connaître les préférences du client de l\'utilisateur',
                        ],
                    ]
                ],
            ]
        ],
        'edit' => [
            'description' => 'Modifier toutes les informations de l\'utilisateur',
            'scopes' => [
                'identity' => [
                    'description' => 'Modifier l\'identité de l\'utilisateur',
                    'scopes' => [
                        'email' => [
                            'description' => 'Modifier l\'adresse email de l\'utilisateur',
                        ],
                        'names' => [
                            'description' => 'Modifier les nom et prénom de l\'utilisateur',
                        ],
                        'auth' => [
                            'description' => 'Modifier les types de connexions de l\'utilisateur',
                            'scopes' => [
                                'cas' => [
                                    'description' => 'Modifier les données CAS de l\'utilisateur',
                                ],
                                'password' => [
                                    'description' => 'Modifier les données liées à la création du compte mot de passe de l\'utilisateur',
                                ],
                                'app' => [
                                    'description' => 'Modifier les données liées à la création des comptes applications de l\'utilisateur',
                                ],
                            ]
                        ],
                    ]
                ],
                'details' => [
                    'description' => 'Modifier tous les détails de l\'utilisateur (date de naissance, âge, majorité)',
                    'scopes' => [
                        'birthdate' => [
                            'description' => 'Modifier la date de naisssance de l\'utilisateur',
                        ],
                    ]
                ],
                'preferences' => [
                    'description' => 'Modifier toutes les préférences de l\'utilisateur (globales, de l\'association, du client)',
                    'scopes' => [
                        'global' => [
                            'description' => 'Modifier les préférences globales de l\'utilisateur',
                        ],
                        'asso' => [
                            'description' => 'Modifier les préférences de l\'association de l\'utilisateur',
                        ],
                        'client' => [
                            'description' => 'Modifier les préférences du client de l\'utilisateur',
                        ],
                    ]
                ],
            ]
        ],
        'create' => [
            'description' => 'Ajouter des informations pour l\'utilisateur',
            'scopes' => [
                'identity' => [
                    'description' => 'Ajouter des informations concernant l\'identité de l\'utilisateur',
                    'scopes' => [
                        'auth' => [
                            'description' => 'Ajouter des types de connexions de l\'utilisateur',
                            'scopes' => [
                                'cas' => [
                                    'description' => 'Ajouter des données CAS de l\'utilisateur',
                                ],
                                'password' => [
                                    'description' => 'Ajouter des données liées à la création du compte mot de passe de l\'utilisateur',
                                ],
                                'app' => [
                                    'description' => 'Ajouter des données liées à la création des comptes applications de l\'utilisateur',
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
                    'description' => 'Modifier tous les détails de l\'utilisateur (date de naissance, âge, majorité)',
                    'scopes' => [
                        'birthdate' => [
                            'description' => 'Modifier la date de naisssance de l\'utilisateur',
                        ],
                        'age' => [
                            'description' => 'Modifier l\'âge de l\'utilisateur',
                        ]
                    ]
                ],
            ]
        ],
    ]
];
