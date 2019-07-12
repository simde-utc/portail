<?php
/**
 * Model corresponding to accesses.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Contracts\Ownership\CanBeOwner;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;

class Access extends Model
{
    protected $table = 'access';

    protected $fillable = [
        'type', 'name', 'description', 'utc_access',
    ];

    protected $must = [
        'type', 'name', 'description',
    ];
}
