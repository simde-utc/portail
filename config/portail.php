<?php

return [
	// Payutc
	'payutc' => [
		'app_key' 	=> env('PAYUTC_KEY', ''),
		'fun_id' 	=> 41,
		'prod' 		=> false,		// Si le serveur est en https : true
		'viaUTC'	=> false,		// Si le serveur passe par le VPN ou le réseau de l'UTC : true
		'trans_url' => 'https://payutc.nemopay.net/validation?tra_id=',
	],
	'cas' => [
		'url'		=> 'https://cas.utc.fr/cas/'
	],
	'ginger_key' 	=> env('GINGER_KEY', '')
];
