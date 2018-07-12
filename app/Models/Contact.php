<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use Cog\Contracts\Ownership\Ownable;

class Contact extends Model implements Ownable
{
    use HasMorphOwner;

    protected $fillable = [
        'value', 'description', 'contact_type_id', 'visibility_id', 'owned_by_id', 'owned_by_type'
    ];

    protected $with = [
        'type', 'visibility'
    ];

    protected $hidden = [
        'visibility_id', 'owned_by_id', 'owned_by_type',
    ];

    public function owned_by() {
        return $this->morphTo();
    }

	public function visibility() {
    	return $this->belongsTo(Visibility::class);
    }

    public function type() {
        return $this->belongsTo(ContactType::class, 'contact_type_id');
    }

	public function user() {
		return $this->morphTo(User::class, 'owned_by');
	}

	public function asso() {
		return $this->morphTo(Asso::class, 'owned_by');
	}

	public function client() {
		return $this->morphTo(Client::class, 'owned_by');
	}

	public function group() {
		return $this->morphTo(Group::class, 'owned_by');
	}
}
