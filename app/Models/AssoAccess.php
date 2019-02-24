<?php
/**
 * Modèle correspondant aux accès des associations.
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
     * Relation avec l'association.
     *
     * @return mixed
     */
    public function asso()
    {
        return $this->belongsTo(Asso::class);
    }

    /**
     * Relation avec le membre.
     *
     * @return mixed
     */
    public function member()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'utilisateur ayant confirmé.
     *
     * @return mixed
     */
    public function confirmed_by()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'utilisateur ayant validé.
     *
     * @return mixed
     */
    public function validated_by()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'accès.
     *
     * @return mixed
     */
    public function access()
    {
        return $this->belongsTo(Access::class);
    }

    /**
     * Relation avec le semestre.
     *
     * @return mixed
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
