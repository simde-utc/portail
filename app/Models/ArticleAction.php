<?php

namespace App\Models;

use App\Traits\Model\HasKeyValue;

class ArticleAction extends Model
{
	use HasKeyValue;

	protected $table = 'articles_actions';

	protected $fillable = [
		'article_id', 'user_id', 'key', 'value', 'type', 'visibility_id'
	];

	protected $with = [
		'created_by', 'owned_by', 'visibility',
	];

	protected $withModelName = [
		'created_by', 'owned_by',
	];

	protected $must = [
		'title', 'description', 'owned_by', 'created_at',
	];

	protected $hidden = [
		'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'visibility_id',
	];

	public function article() {
		return $this->belongsTo(Article::class);
	}

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function visibility() {
		return $this->belongsTo(Visibility::class);
	}
}
