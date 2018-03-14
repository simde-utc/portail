<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visibility extends Model
{
    protected $table = 'visibilities';

    protected $fillable = [
        'type', 'name', 'parent_id',
    ];

    public function articles() {
        return $this->hasMany('App\Models\Article');
    }

    public function events() {
        return $this->hasMany('App\Models\Event');
    }

    // TODO les liens vers les modèles
    // TODO générer isVisibible($user_id) en fonction des droits de la personne de visibilité (passer par un service ?)
}
