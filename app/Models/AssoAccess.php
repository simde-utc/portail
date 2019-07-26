<?php
/**
 * Model corresponding to association accesses.
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

class AssoAccess extends Model
{
    protected $table = 'assos_access';

    protected $fillable = [
        'asso_id', 'member_id', 'confirmed_by_id', 'access_id', 'semester_id', 'validated_by_id', 'validated_at',
        'description', 'comment', 'validated'
    ];

    protected $with = [
        'asso', 'member', 'confirmed_by', 'validated_by', 'access', 'semester',
    ];

    protected $hidden = [
        'asso_id', 'member_id', 'confirmed_by_id', 'validated_by_id', 'access_id', 'semester_id',
    ];

    protected $must = [
        'asso', 'member', 'confirmed_by', 'access', 'semester', 'validated', 'validated_at'
    ];

    protected $casts = [
        'validated' => 'boolean',
    ];

    /**
     * Relation with the association.
     *
     * @return mixed
     */
    public function asso()
    {
        return $this->belongsTo(Asso::class);
    }

    /**
     * Relation with the member.
     *
     * @return mixed
     */
    public function member()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation with the user who confirmed.
     *
     * @return mixed
     */
    public function confirmed_by()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation with the user who validated.
     *
     * @return mixed
     */
    public function validated_by()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation with the access.
     *
     * @return mixed
     */
    public function access()
    {
        return $this->belongsTo(Access::class);
    }

    /**
     * Relation with the semester.
     *
     * @return mixed
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
