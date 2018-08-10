<?php

namespace App\Models;

use Laravel\Passport\Client as PassportClient;
use App\Interfaces\Model\CanHaveCalendars;
use App\Interfaces\Model\CanHaveEvents;
use App\Interfaces\Model\CanHaveArticles;
use App\Traits\Model\HasHiddenData;
use NastuzziSamy\Laravel\Traits\HasSelection;

class Client extends PassportClient implements CanHaveCalendars, CanHaveEvents, CanHaveArticles
{
    use HasHiddenData, HasSelection;

    protected $fillable = [
        'user_id', 'name', 'secret', 'redirect', 'personal_access_client', 'password_client', 'revoked', 'created_at', 'updated_at', 'asso_id', 'scopes'
    ];

    protected $selection = [
        'paginate' => null,
    ];

    public function asso() {
        return $this->belongsTo(Asso::class);
    }

    public function calendars() {
    	return $this->morphMany(Calendar::class, 'owned_by');
    }

    public function events() {
    	return $this->morphMany(Event::class, 'owned_by');
    }

    public function articles() {
    	return $this->morphMany(Article::class, 'owned_by');
    }

	public function isCalendarAccessibleBy(int $user_id): bool {
		return $this->asso()->currentMembers->wherePivot('user_id', $user_id)->exists();
	}

	public function isCalendarManageableBy(int $user_id): bool {
		return $this->asso()->hasOneRole('developer', ['user_id' => $user_id]);
	}

	public function isEventAccessibleBy(int $user_id): bool {
		return $this->asso()->currentMembers->wherePivot('user_id', $user_id)->exists();
	}

	public function isEventManageableBy(int $user_id): bool {
		return $this->asso()->hasOneRole('developer', ['user_id' => $user_id]);
	}

	public function isArticleAccessibleBy(int $user_id): bool {
		return $this->asso()->currentMembers->wherePivot('user_id', $user_id)->exists();
	}

	public function isArticleManageableBy(int $user_id): bool {
		return $this->asso()->hasOneRole('developer', ['user_id' => $user_id]);
	}
}
