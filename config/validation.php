<?php

/*
|--------------------------------------------------------------------------
|	Taille des validations
|--------------------------------------------------------------------------
| Utile pour les Requests et les Migrations
|
| helper pour les requests :
|		validation_between('login') 	=> donne between:1,15
| helper pour les migrations :
| 		validation_max('login') 		=> donne 15
*/

return [
	// Login pour les assos et les étudiants
	'login' => [
		'min' => 1,
		'max' => 15
	],

	'type' => [
		'min' => 1,
		'max' => 31,
	],

	'email' => [
		// 'email' => true,
		'min' => 7,
		'max' => 127
	],

	'url' => [
		'min' => 7,
		'max' => 255
	],

	// Nom et prénom de personnes
	'name' => [
		'min' => 1,
		'max' => 63
	],

	// Titre d'articles et d'évènements
	'title' => [
		'min' => 1,
		'max' => 127
	],

	// Description courte d'assos
	'description' => [
		'min' => 0,
		'max' => 8191
	],

	// Chaine de charactère courte basique
	'string' => [
		'min' => 0,
		'max' => 255
	],

	// Contenu d'article
	'article' => [
		'min' => 0,
		'max' => 16383
	],

	// Commentaire d'article
	'comment' => [
		'min' => 1,
		'max' => 4095
	],

	//


];
