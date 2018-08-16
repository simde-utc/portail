<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function article() {
        return $this->morphedByMany('App\Models\Article', 'used_by', 'tags_used');
    }
}
