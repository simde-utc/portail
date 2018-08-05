<?php

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;

class Article extends Model implements OwnableContract
{
	use HasMorphOwner;

	protected $table = 'articles';

	protected $fillable = [
		'title', 'description', 'content', 'image', 'event_id', 'visibility_id', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type',
	];

	protected $with = [
		'created_by', 'owned_by', 'tags', 'visibility', 'event',
	];

	protected $withModelName = [
		'created_by', 'owned_by',
	];

	protected $must = [
		'title', 'description', 'owned_by', 'created_at',
	];

	protected $hidden = [
		'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'visibility_id', 'event_id',
	];

	protected $selection = [
		'paginate' 	=> 10,
		'order'		=> [
			'default' 	=> 'latest',
			'columns'	=> [
				'name' 	=> 'title',
			],
		],
		'month'		=> null,
		'week'		=> null,
		'day'		=> null,
		'interval'	=> null,
		'date'		=> null,
		'dates'		=> null
	];

	public function getDescriptionAttribute() {
		$description = $this->getOriginal('description');

		if ($description)
			return $description;
		else
			return trimText($this->getAttribute('content'), validation_max('string'));
	}

	public function created_by() {
		return $this->morphTo();
	}

	public function owned_by() {
		return $this->morphTo();
	}

	public function tags() {
		return $this->morphToMany(Tag::class, 'used_by', 'tags_used');
	}

	public function visibility() {
		return $this->belongsTo(Visibility::class, 'visibility_id');
	}

	public function event() {
		return $this->belongsTo(Event::class);
	}
}
