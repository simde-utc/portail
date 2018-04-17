<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    protected $table = 'articles';

    protected $fillable = [
        'title', 'content', 'image', 'toBePublished', 'visibility_id', 'asso_id',
    ];

    public static function create($data){
    	$instance = static::query()->create($data);
    	$instance->collaborators()->attach($data['asso_id']);
    	return $instance;
    }

    public function collaborators() {
  		return $this->belongsToMany('App\Models\Asso', 'articles_collaborators');
  	}

  	public function asso(){
    	return $this->belongsTo('App\Models\Asso');
    }
}
