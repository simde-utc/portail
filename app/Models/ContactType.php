<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactType extends Model
{
    protected $table = 'contacts_types';
    protected $hidden = ['id', 'created_at', 'updated_at', 'pattern', 'max'];
    // Natan : On cache le pattern car celui ci nous sert uniquement lorsque l'on crée ou update un contact, on ne le donne pas en response.

    public function contacts() {
        return $this->hasMany('App\Models\Contact');
    }
}
