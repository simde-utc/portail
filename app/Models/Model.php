<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use App\Traits\Model\HasHiddenData;

abstract class Model extends BaseModel
{
    use HasHiddenData;
}
