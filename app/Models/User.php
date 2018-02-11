<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use Notifiable;

	// La PK n'est pas un id autoincrémenté
	public $incrementing = false;
	protected $primaryKey = 'login';

	/**
	 * The attributes that are mass assignable.
	 */
	protected $fillable = [
		'login', 'prenom', 'nom', 'email', 'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 */
	protected $hidden = [
		'password', 'remember_token',
	];




}
