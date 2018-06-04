<?php

namespace App\Models;

use Ginger;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasKeyValue;
use App\Models\User;

class UserDetail extends Model
{
	use HasKeyValue;

	public $incrementing = false; // L'id n'est pas autoincrementé
	protected $table = 'users_details';
	protected $primaryKey = ['user_id', 'key'];
	protected $fillable = [
		'user_id', 'key', 'value', 'type',
	];
	/* Vérifier les fcts ! */
	public function age($query) {
		$birthdate = $query->valueOf('birthdate');

		if ($birthdate)
			return $birthdate->age;
		else
			throw new PortailException('Non trouvé');
	}

	public function isMajor($query) {
		$cas = $query->first()->user->cas;

		if ($cas) {
			try {
				return Ginger::user($cas->login)->isAdult();
			}
			catch (\Exception $e) {} // Si on a pas les données CAS, on regarde avec la date de naissance
		}

		$age = $this->age($query);

		if ($age)
			return $age >= 18;
		else
			return null;
	}

	public function isMinor($query) {
		$isMajor = $this->isMajor($query);

		if (is_null($isMajor))
			return null;
		else
			return !$isMajor;
	}

	public function loginCAS($query) {
		$cas = $query->first()->user->cas;

		if ($cas)
			return $cas->login;
		else
			return null;
	}

	public function loginContributorBde($query) {
		$casLogin = $this->loginCAS($query);

		if ($casLogin)
			return $casLogin;
		else
			return Ginger::userByEmail($query->first()->user->email)->getLogin();
	}

	public function isContributorBde($query) {
		$login = $this->loginContributorBde($query);

		if ($login) {
			try {
				return Ginger::user($login)->isContributor();
			}
			catch (\Exception $e) {}
		}

		return null;
	}
}
