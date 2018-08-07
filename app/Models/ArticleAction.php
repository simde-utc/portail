<?php

namespace App\Models;

use App\Traits\Model\HasKeyValue;

class ArticleAction extends Model
{
	use HasKeyValue;

	public $incrementing = false;

	protected $table = 'articles_actions';

	protected $primaryKey = [
		'article_id', 'user_id', 'key'
	];

	protected $fillable = [
		'article_id', 'user_id', 'key', 'value', 'type'
	];

	protected $must = [
		'created_at',
	];

	protected $hidden = [
		'visibility_id',
	];

	public function article() {
		return $this->belongsTo(Article::class);
	}

	public function user() {
		return $this->belongsTo(User::class);
	}
}
