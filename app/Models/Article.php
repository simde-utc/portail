<?php

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;

class Article extends Model implements OwnableContract
{
	use HasMorphOwner;

	protected $table = 'articles';

	protected $fillable = [
		'title', 'content', 'image', 'visibility_id', 'created_by_id', 'created_by_tye', 'owned_by_id', 'owned_by_type',
	];

	protected $with = [
		'created_by',
		'owned_by',
	//	'tags'
	];

	protected $appends = [
		'collaborators',
	];

	public function created_by() {
		return $this->morphTo();
	}

	public function owned_by() {
		return $this->morphTo();
	}

	public function getCollaboratorsAttribute() {
		return $this->collaborators()->get();
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
		return $this->morphToMany('App\Models\Tag', 'tags_used');
	}

	public function visibility() {
		return $this->belongsTo(Visibility::class, 'visibility_id');
	}
}
