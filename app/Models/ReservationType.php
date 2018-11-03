<?php
/**
 * Modèle correspondant aux événements.
 *
 * @author Thomas Meurou <thomas.meurou@yahoo.fr>
 * @author Josselin Pennors <josselin.pennors@hotmail.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

class ReservationType extends Model
{
    protected $table = 'rooms_reservations_types';

    protected $fillable = [
        'name', 'type', 'need_validation',
    ];

    protected $casts = [
        'need_validation' => 'boolean',
    ];

    protected $must = [
        'name', 'type', 'need_validation',
    ];

    /**
     * Relation avec les relations.
     *
     * @return mixed
     */
    protected function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
