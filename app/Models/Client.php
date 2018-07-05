<?php

namespace App\Models;

use Laravel\Passport\Client as PassportClient;
use App\Interfaces\CanHaveCalendars;

class Client extends PassportClient implements CanHaveCalendars
{
    protected $fillable = [
        'user_id', 'name', 'secret', 'redirect', 'personal_access_client', 'password_client', 'revoked', 'created_at', 'updated_at', 'asso_id', 'scopes'
    ];

    public function asso() {
        return $this->belongsTo(Asso::class);
    }

	public function isCalendarAccessibleBy(int $user_id): bool {
		return $this->asso()->isCalendarAccessibleBy($user_id);
	}

	public function isCalendarManageableBy(int $user_id): bool {
		return $this->asso()->isCalendarManageableBy($user_id);
	}
}
