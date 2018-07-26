<?php

namespace App\Models;

class ContactType extends Model
{
    protected $table = 'contacts_types';

    protected $fillable = [
        'name', 'pattern'
    ];

    protected $hidden = [
        'id', 'created_at', 'updated_at',
    ];

    protected $must = [
        'pattern',
    ];

    public function contacts() {
        return $this->hasMany(Contact::class);
    }
}
