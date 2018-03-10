<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name', 'user_id', 'icon_id', 'is_public', 'is_active',
    ];

  	protected $casts = [
        'is_public' => 'boolean',
  		  'is_active' => 'boolean',
  	];

    public function icon() {
        return $this->hasOne('App\Models\Icon');
    }

		public function members() {
				return $this->belongsToMany('App\Models\User', 'groups_members');
		}
}
