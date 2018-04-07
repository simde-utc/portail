<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssoMember extends Model
{
    protected $table = 'assos_members';

    protected $fillable = [
        'asso_id', 'user_id', 'semester_id',
    ];

		public function asso() {
				return $this->belongsTo('App\Models\Asso');
		}

		public function user() {
				return $this->belongsTo('App\Models\User');
		}

		public function semester() {
				return $this->belongsTo('App\Models\Semester');
		}
}
