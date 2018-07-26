<?php

namespace App\Models;

class Article extends Model // TODO transformer en Morph // TODO with / must
{
	protected $table = 'articles';

	protected $fillable = [
		'title', 'content', 'image', 'toBePublished', 'visibility_id', 'asso_id',
	];

	protected $with = [
		'collaborators:id,shortname',
		'tags:name,description'
	];

	public static function boot() {
		static::created(function ($model) {
			$model->collaborators()->attach($model['asso_id']);
		});
	}

	public function collaborators() {
		return $this->belongsToMany(Asso::class, 'articles_collaborators');
	}

	public function asso() {
		return $this->belongsTo(Asso::class);
	}

	public function tags() {
		return $this->morphToMany('App\Models\Tag', 'tags_used');
	}

	public function visibility() {
		return $this->belongsTo(Visibility::class, 'visibility_id');
	}

	public function comments() {
		return $this->morphMany('App\Models\Comment', 'commentable');
	}
}
