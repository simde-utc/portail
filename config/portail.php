<?php

return [
	// Gestion des versions du Portail
	'versions' => [
		'v1'
	],
	// Version actuelle du serveur (en dessous: déprécié, au dessus: en beta)
	'version' => 'v1',

	// Les headers spécifiques au Portail (commençant par X-Portail)
	'headers' => [
		'request_type' 	=> 'X-Portail-Request-Type',
		'warn'			=> 'X-Portail-Warn',
		'version'		=> 'X-Portail-Version',
	],

	// Payutc
	'payutc' => [
		'app_key' 	=> env('PAYUTC_KEY', ''),
		'fun_id' 	=> 41,
		'prod' 		=> false,		// Si le serveur est en https : true
		'viaUTC'	=> false,		// Si le serveur passe par le VPN ou le réseau de l'UTC : true
		'trans_url' => 'https://payutc.nemopay.net/validation?tra_id=',
	],

	// Définition des rôles
	'roles' => [
		// Roles admins
		'admin' => [
			'users' => 'admin',
			'assos' => 'president',
			'groups' => 'group admin',
		],
	],

	// Association gérant le portail:
	'assos' => [
		env('APP_ASSO', 'simde') => [
			'president' => 'superadmin',
			'bureau' => 'admin',
		],
		'bde' => [
			'president' => 'admin'
		]
	],

	'cas' => [
		'url'		=> 'https://cas.utc.fr/cas/'
	],

	'ginger_key' 	=> env('GINGER_KEY', ''),

	'cookie_lifetime' => 518400,
];
