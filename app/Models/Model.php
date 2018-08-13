<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use App\Traits\Model\HasHiddenData;
use NastuzziSamy\Laravel\Traits\HasSelection;
use App\Traits\Model\HasBinaryUuid;

abstract class Model extends BaseModel
{
    use HasHiddenData, HasSelection, HasBinaryUuid;
}
