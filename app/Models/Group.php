<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
  protected $fillable = [
    'name', 'icon_id', 'is_public'
  ];

  public function icon() {
    return $this->hasOne('App\Models\Icon');
  }
}
