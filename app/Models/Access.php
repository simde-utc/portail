<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Contracts\Ownership\CanBeOwner;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;

class Access extends Model
{
	protected $table = 'access';

	protected $fillable = [
		'type', 'name', 'description', 'data',
	];

	protected $casts = [
		'data' => 'array',
	];

	protected $must = [
		'type', 'name', 'description',
	];
}
