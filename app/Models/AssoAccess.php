<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Contracts\Ownership\CanBeOwner;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;

class AssoAccess extends Model
{
	protected $table = 'assos_access';

	protected $fillable = [
		'asso_id', 'member_id', 'confirmed_by_id', 'access_id', 'semester_id', 'validated_by_id', 'validated', 'description', 'comment',
	];

	protected $casts = [
		'validated' => 'boolean',
	];

	protected $with = [
		'asso', 'member', 'confirmed_by', 'validated_by', 'access', 'semester',
	];

	protected $hidden = [
		'asso_id', 'member_id', 'confirmed_by_id', 'validated_by_id', 'access_id', 'semester_id',
	];

	protected $must = [
		'asso', 'member', 'confirmed_by', 'access', 'semester', 'validated',
	];

	public function asso() {
		return $this->belongsTo(Asso::class);
	}

	public function member() {
		return $this->belongsTo(User::class);
	}

	public function confirmed_by() {
		return $this->belongsTo(User::class);
	}

	public function validated_by() {
		return $this->belongsTo(User::class);
	}

	public function acces() {
		return $this->belongsTo(Access::class);
	}

	public function semester() {
		return $this->belongsTo(Semester::class);
	}
}
