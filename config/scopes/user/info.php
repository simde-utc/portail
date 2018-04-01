<?php

/*
 * Liste des scopes en fonction des routes
 *   - Définition des scopes:
 *   	portée + "-" + verbe + "-" + categorie + (pour chaque sous-catégorie: '-' + sous-catégorie)
 *   	ex: user-get-user user-get-user-assos user-get-user-assos-followed
 *
 *   - Définition de la portée des scopes:
 *     + user :    user_credential => nécessite que l'application soit connecté à un utilisateur
 *     + client :  client_credential => nécessite que l'application est les droits d'application indépendante d'un utilisateur
 *
 *   - Définition du verbe:
 *     + manage:  gestion de la ressource entière
 *       + get :  récupération des informations en lecture seule
 *       + set :  posibilité d'écrire et modifier les données
 *         + create:  créer une donnée associée
 *         + edit:    modifier une donnée
 *         + remove:  supprimer une donnée
 */

// Toutes les routes commencant par user-{verbe}-info-
return [
	'description' => 'Informations personnelles',
	'icon' => 'user-circle',
	'verbs' => [
		'manage' => [
			'description' => 'Gérer totalement les informations sur l\'utilisateur',
			'scopes' => [
				'identity' => [
					'description' => 'Gérer l\'identité de l\'utilisateur',
				]
			]
		],
		'get' => [
			'description' => 'Récupérer toutes les informations sur l\'utilisateur',
			'scopes' => [
				'identity' => [
					'description' => 'Récupérer l\'identité de l\'utilisateur',
				]
			]
		],
		'set' => [
			'description' => "Modifier toutes les informations sur l'utilisateur",
		],
		'create' => [
			'description' => "Créer toutes les informations sur l'utilisateur",
		],
		'edit' => [
			'description' => "Modifier toutes les informations sur l'utilisateur",
			'scopes' => [
				'identity' => [
					'description' => 'Modifier l\'identité de l\'utilisateur',
				]
			]
		],
	]
];
