<?php
/**
 * Modèle correspondant aux types des associations.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

class AssoType extends Model
{
    protected $table = 'assos_types';

    protected $fillable = [
        'type', 'name',
    ];

    protected $must = [
        'type', 'name',
    ];

    /**
     * Relation avec l'association.
     *
     * @return mixed
     */
    public function asso()
    {
        return $this->hasMany(Asso::class);
    }
}
