<?php

namespace App\Models;

use Ginger;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasKeyValue;
use App\Models\User;

class UserDetails extends Model
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
		return (new static)->birthdate($user_id)->age;
	}

	public static function major(int $user_id) {
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

	public static function minor(int $user_id) {
		return !(new static)->major($user_id);
	}
}
