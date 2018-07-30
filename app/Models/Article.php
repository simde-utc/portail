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
		'created_by', 'owned_by', 'tags', 'visibility'
	]; // On ne peut pas mettre collabortors !

	protected $must = [
		'title',
	];

	protected $hidden = [
		'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'visibility_id'
	];

	public function hideSubData(bool $addSubModelName = false) {
		parent::hideSubData($addSubModelName);

		$this->collaborators = $this->collaborators->map(function ($collaborator) use ($addSubModelName) {
			return $collaborator->hideData($addSubModelName)->makeHidden('pivot');
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
		return $this->{\ModelResolver::getName($this->owned_by_type).'_collaborators'}();
	}

	public function asso_collaborators() {
		return $this->morphedByMany(Asso::class, 'collaborator', 'articles_collaborators');
	}

	public function group_collaborators() {
		return $this->morphedByMany(Group::class, 'collaborator', 'articles_collaborators');
	}

	public function user_collaborators() {
		return $this->morphedByMany(User::class, 'collaborator', 'articles_collaborators');
	}

	public function tags() {
		return $this->morphToMany(Tag::class, 'used_by', 'tags_used');
	}

	public function visibility() {
		return $this->belongsTo(Visibility::class, 'visibility_id');
	}
}
