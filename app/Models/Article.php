<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public function assos() {
  		  return $this->belongsToMany('App\Models\Asso', 'assos_articles');
  	}
}
