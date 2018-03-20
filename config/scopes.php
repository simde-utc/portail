<?php

/*
 * Liste des scopes TODO en fonction des routes
 * 	- Définition des scopes:
 * 		+ user : 		user_credential => nécessite que l'application soit connecté à un utilisateur
 * 		+ client : 		client_credential => nécessite que l'application est les droits d'application indépendante d'un utilisateur
 *
 * 	- Définition du verbe:
 * 		+ get : 	récupération des informations en lecture seule
 * 		+ set : 	posibilité d'écrire et modifier les données
 * 		+ create: 	créer une donnée associée
 * 		+ edit:		modifier une donnée
 * 		+ remove:	supprimer une donnée
 * 		+ manage:	gestion de la ressource entière
 */
return [
	// Connexion nécessaire par l'utilisateur
	'user' => [
		// Droits relatifs aux utilisateurs
		'get-user' => "Récupérer les informations de l'utilisateur",
		'set-user' => "Modifier les information de l'utilisateur",
		'manage-user' => "Gérer les information de l'utilisateur",

		// Droits relatifs aux assos
		'get-user-assos-currently-done' => "Récupérer les associations actuellement faites par l'utilisateur",
		'get-user-assos-currently-followed' => "Récupérer les associations actuellement suivies par l'utilisateur",
		'get-user-assos-currently' => "Récupérer les associations actuellement suivies et faites par l'utilisateur",
		'get-user-assos-done' => "Récupérer les associations faites par l'utilisateur",
		'get-user-assos-followed' => "Récupérer les associations suivies par l'utilisateur",
		'get-user-assos' => "Récupérer les associations suivies et faites par l'utilisateur",
		'get-user-assos-members' => "Récupérer les membres des associations faites par l'utilisateur",
		'set-user-assos' => "Gérer la création et la modification des associations suivies et faites par l'utilisateur",
	],

	// Connexion nécessaire que par l'application
	'client' => [
		// Droits relatifs aux utilisateurs
		'get-users' => "Récupérer la liste des utilisateurs",
		'set-users' => "Gérer la création et la modification d'utilisateurs",
		'manage-users' => "Gestion des utilisateurs",

		// Droits relatifs aux assos
		'get-assos' => "Récupérer la liste des associations",
		'set-assos' => "Créer et modifier des associations",
		'manage-assos' => "Gestion des associations",
		'get-assos-currently-members-done' => "Récupérer les actuels membres des associations",
		'get-assos-currently-members-followed' => "Récupérer les actuels suiveurs des associations",
		'get-assos-currently-members' => "Récupérer les actuels membres et suiveurs des associations",
		'get-assos-members-done' => "Récupérer les membres des associations",
		'get-assos-members-followed' => "Récupérer les suiveurs des associations",
		'get-assos-members' => "Récupérer les membres et suiveurs des associations",
	],

	// Surement d'autres à ajouter pour chaque section haha
];

//!\\ Attention ! Pas de scope super admin, nécessité de découper les droits le plus petitement possible !
