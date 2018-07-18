<?php

namespace App\Models;

class Article extends Model // TODO transformer en Morph
{
	protected $table = 'articles';

	protected $fillable = [
		'title', 'content', 'image', 'toBePublished', 'visibility_id', 'asso_id',
	];

	public static function boot() {
		static::created(function ($model) {
			$model->collaborators()->attach($model['asso_id']);
		});
	}

	public function hideData(array $params = []): Model {
		return $this; // TODO
	}

	public function collaborators() {
		return $this->belongsToMany('App\Models\Asso', 'articles_collaborators');
	}

	public function asso() {
		return $this->belongsTo('App\Models\Asso');
	}

	public function visibility() {
		return $this->belongsTo('App\Models\Visibility', 'visibility_id');
	}
}
