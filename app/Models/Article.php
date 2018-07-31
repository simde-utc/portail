<?php

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;

class Article extends Model implements OwnableContract
{
	use HasMorphOwner;

	protected $table = 'articles';

	protected $fillable = [
		'title', 'content', 'image', 'visibility_id', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type',
	];

	protected $with = [
		'created_by', 'owned_by', 'tags', 'visibility', 'event',
	]; // On ne peut pas mettre collaborators !

	protected $withModelName = [
		'created_by', 'owned_by', 'collaborators',
	];

	protected $must = [
		'title', 'owned_by', 'created_at',
	];

	protected $hidden = [
		'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'visibility_id', 'event_id',
	];

	public function hideSubData(bool $addSubModelName = false) {
		parent::hideSubData($addSubModelName);

		$this->collaborators = $this->collaborators->map(function ($collaborator) use ($addSubModelName) {
			return $collaborator->hideData($addSubModelName || in_array('collaborators', $this->withModelName))->makeHidden('pivot');
		});

		return $this;
	}

	public function created_by() {
		return $this->morphTo();
	}

	public function owned_by() {
		return $this->morphTo();
	}

	public function collaborators() {
		return $this->{\ModelResolver::getName($this->owned_by_type).'Collaborators'}();
	}

	public function assoCollaborators() {
		return $this->morphedByMany(Asso::class, 'collaborator', 'articles_collaborators');
	}

	public function groupCollaborators() {
		return $this->morphedByMany(Group::class, 'collaborator', 'articles_collaborators');
	}

	public function userCollaborators() {
		return $this->morphedByMany(User::class, 'collaborator', 'articles_collaborators');
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
