<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $table = 'groups_members';

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
