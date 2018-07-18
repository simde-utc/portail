<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use App\Interfaces\Model\CanHideData;

abstract class Model extends BaseModel implements CanHideData
{
    public function getModelAttribute() {
        return \ModelResolver::getName($this);
    }
}
