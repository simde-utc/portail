<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
  protected $fillable = [
    'user_id', 'group_id',
  ];

  public function user() {
    return $this->hasOne('App\Models\User');
  }

  public function group() {
    return $this->hasOne('App\Models\Group');
  }
}
