<?php

namespace App\Models;

abstract class Auth extends Model // TODO with must fillable
{
	public $incrementing = false; // L'id n'est pas autoincrementé
	protected $primaryKey = 'user_id';

	public function user() {
		return $this->belongsTo(User::class);
	}

	// Fonctions permettant de vérifier la connexion d'un utilisateur en fonction des différents types d'authentification
	public abstract function getUserByIdentifiant($username);
	public abstract function isPasswordCorrect($password);
}
