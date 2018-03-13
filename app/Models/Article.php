<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    protected $table = 'articles';

    protected $fillable = [
        'title', 'content', 'image', 'toBePublished', 'visibility_id', 'asso_id'
    ];

    public function assos() {
  		  return $this->belongsToMany('App\Models\Asso', 'assos_articles');
  	}
}
