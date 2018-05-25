<?php

namespace App\Models;

use Ginger;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasKeyValue;
use App\Models\User;

class UserDetail extends Model
{
	use HasKeyValue;

	protected $table = 'users_details';
	protected $primaryKey = 'user_id';
	protected $fillable = [
		'user_id', 'key', 'value', 'type',
	];
	protected $valuesInFunction = [
		'age', 'major', 'minor',
	];

	public static function age(int $user_id) {
		$birthdate = self::birthdate($user_id);

		if ($birthdate)
			return $birthdate->age;
		else
			return null;
	}

	public static function isMajor(int $user_id) {
		$cas = User::find($user_id)->cas;

		if ($cas) {
			try {
				return Ginger::user($cas->login)->isAdult();
			}
			catch (\Exception $e) {} // Si on a pas les données CAS, on regarde avec la date de naissance
		}

		$age = (new static)->age($user_id);

		if ($age)
			return $age >= 18;
		else
			return null;
	}

	public static function isMinor(int $user_id) {
		$isMajor = self::isMajor($user_id);

		if (is_null($isMajor))
			return null;
		else
			return !$isMajor;
	}

	public static function loginCAS(int $user_id) {
		$cas = User::find($user_id)->cas;

		if ($cas)
			return $cas->login;
		else
			return null;
	}

	public static function loginContributorBde(int $user_id) { // TODO pour les extés
		$cas = User::find($user_id)->cas;

		if ($cas)
			return $cas->login;
		else
			return Ginger::userByEmail(User::find($user_id)->email)->getLogin();
	}

	public static function isContributorBde(int $user_id) {
		$login = self::loginContributorBde($user_id);

		if ($login) {
			try {
				return Ginger::user($login)->isContributor();
			}
			catch (\Exception $e) {} // Si on a pas les données CAS, on regarde avec la date de naissance
		}

		return null;
	}
}
