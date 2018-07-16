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

// Toutes les routes commencant par user-{verbe}-contacts-
return [
	'description' => 'Contacts',
	'icon' => 'address-card',
	'verbs' => [
		'manage' => [
			'description' => 'Gérer les moyens et les informations de contact de l\'utilisateur, de ses associations et de ses groupes',
			'scopes' => [
				'users' => [
					'description' => 'Gérer les moyens et les informations de contact de l\'utilisateur',
				],
				'assos' => [
					'description' => 'Gérer les moyens et les informations de contact des associations de l\'utilisateur',
				],
				'groups' => [
					'description' => 'Gérer les moyens et les informations de contact des groupes de l\'utilisateur',
				],
			]
		],
		'set' => [
			'description' => 'Modifier et ajouter des moyens et des informations de contact de l\'utilisateur, de ses associations et de ses groupes',
			'scopes' => [
				'users' => [
					'description' => 'Modifier et ajouter des moyens et des informations de contact de l\'utilisateur',
				],
				'assos' => [
					'description' => 'Modifier et ajouter des moyens et des informations de contact des associations de l\'utilisateur',
				],
				'groups' => [
					'description' => 'Modifier et ajouter des moyens et des informations de contact des groupes de l\'utilisateur',
				],
			]
		],
		'create' => [
			'description' => 'Ajouter des moyens et des informations de contact de l\'utilisateur, de ses associations et de ses groupes',
			'scopes' => [
				'users' => [
					'description' => 'Ajouter des moyens et des informations de contact de l\'utilisateur',
				],
				'assos' => [
					'description' => 'Ajouter des moyens et des informations de contact des associations de l\'utilisateur',
				],
				'groups' => [
					'description' => 'Ajouter des moyens et des informations de contact des groupes de l\'utilisateur',
				],
			]
		],
		'get' => [
			'description' => 'Récupérer les moyens et les informations de contact de l\'utilisateur, de ses associations et de ses groupes',
			'scopes' => [
				'users' => [
					'description' => 'Récupérer les moyens et les informations de contact de l\'utilisateur',
				],
				'assos' => [
					'description' => 'Récupérer les moyens et les informations de contact des associations de l\'utilisateur',
				],
				'groups' => [
					'description' => 'Récupérer les moyens et les informations de contact des groupes de l\'utilisateur',
				],
			]
		],
	]
];
