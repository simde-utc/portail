<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\Visibility\BaseVisibility;

class Group extends Model
{
	protected $fillable = [
        'name', 'user_id', 'icon_id', 'visibility_id', 'is_active',
    ];

  	protected $casts = [
  		'is_active' => 'boolean',
  	];

    public function icon() {
    	return $this->hasOne('App\Models\Icon');
    }

    public function visibility() {
    	return $this->hasOne('App\Models\Visibility');
    }

  	public function members() {
  		return $this->belongsToMany('App\Models\User', 'groups_members');
  	}
}
