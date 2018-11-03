<?php
/**
 * Modèle de base.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use App\Traits\Model\HasHiddenData;
use App\Traits\Model\HasUuid;
use NastuzziSamy\Laravel\Traits\HasSelection;

abstract class Model extends BaseModel
{
    use HasHiddenData, HasSelection, HasUuid;

    public $incrementing = false;
}
