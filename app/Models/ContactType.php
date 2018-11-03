<?php
/**
 * Modèle correspondant aux types de contacts.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

class ContactType extends Model
{
    protected $table = 'contacts_types';

    protected $fillable = [
        'name', 'type', 'pattern'
    ];

    protected $hidden = [
        'id', 'created_at', 'updated_at',
    ];

    protected $must = [
        'type', 'pattern',
    ];

    /**
     * Relation avec les contacts.
     *
     * @return mixed
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
