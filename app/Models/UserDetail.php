<?php

namespace App\Models;

use Ginger;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasKeyValue;
use App\Models\User;
use App\Exceptions\PortailException;

class UserDetail extends Model
{
	use HasKeyValue;

	public $incrementing = false; // L'id n'est pas autoincrementé
	protected $table = 'users_details';
	protected $primaryKey = ['user_id', 'key'];
	protected $fillable = [
		'user_id', 'key', 'value', 'type',
	];
	protected $functionalKeys = [
		'age', 'isMajor', 'loginCAS', 'loginContributorBde', 'isContributorBde'
	];

	/**
	 * Permet de retrouver l'utilisateur sur lequel on travaille à partir d'un query
	 */
	protected function getUserFromQuery($query) {
		$wheres = $query->getQuery()->wheres;

		foreach ($wheres as $where) {
			if ($where['column'] === $this->getTable().'.'.$this->primaryKey[0]	&& $where['operator'] === '=')
				return User::find($where['value']);
		}

		return null;
	}

	public function age($query) {
		$birthdate = $query->valueOf('birthdate');

		if ($birthdate)
			return $birthdate->age;
		else
			throw new PortailException('Non trouvé');
	}

	public function isMajor($query) {
		$casLogin = $this->loginCAS($query);

		if ($casLogin) {
			try {
				return Ginger::user($casLogin)->isAdult();
			}
			catch (\Exception $e) {} // Si on a pas les données CAS, on regarde avec la date de naissance
		}

		$age = $this->age($query);

		if ($age)
			return $age >= 18;
		else
			throw new PortailException('Non trouvé');
	}

	public function loginCAS($query) {
		try {
			$cas = $this->getUserFromQuery($query)->cas;
		} catch (\ErrorException $e) {
			$cas = null;
		}

		if ($cas)
			return $cas->login;
		else
			throw new PortailException('Non trouvé');
	}

	public function loginContributorBde($query) {
		$casLogin = $this->loginCAS($query);

		if ($casLogin)
			return $casLogin;
		else {
			try {
				return Ginger::userByEmail($this->getUserFromQuery($query)->email)->getLogin();
			} catch (\ErrorException $e) {
				throw new PortailException('Non trouvé');
			}
		}
	}

	public function isContributorBde($query) {
		$login = $this->loginContributorBde($query);

		if ($login) {
			try {
				return Ginger::user($login)->isContributor();
			}
			catch (\Exception $e) {}
		}

		throw new PortailException('Non trouvé');
	}
}
