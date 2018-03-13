<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssoArticle extends Model
{
    protected $table = 'assos_articles';

    protected $fillable = [
        'asso_id', 'article_id'
    ];

    public function asso(){
        return $this->hasOne('App\Models\Asso');
    }

    public function article(){
    	return $this->hasOne('App\Models\Article');
    }

}
