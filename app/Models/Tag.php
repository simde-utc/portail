<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function article() {
        return $this->morphedByMany('App\Models\Article', 'tags_used');
    }
}
