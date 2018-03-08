<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
  protected $fillable = [
    'name', 'icon_id', 'is_public'
  ];

	protected $casts = [
		'is_public' => 'boolean', // Si on se connecte via passsword, on désactive tout ce qui est relié au CAS car on suppose qu'il n'est plus étudiant
	];

  public function icon() {
    return $this->hasOne('App\Models\Icon');
  }
}
