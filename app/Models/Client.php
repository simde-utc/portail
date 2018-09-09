<?php

namespace App\Models;

use Laravel\Passport\Client as PassportClient;
use App\Interfaces\Model\CanHaveCalendars;
use App\Interfaces\Model\CanHaveEvents;
use App\Interfaces\Model\CanHaveArticles;
use App\Interfaces\Model\CanNotify;
use App\Traits\Model\HasHiddenData;
use App\Traits\Model\HasUuid;
use NastuzziSamy\Laravel\Traits\HasSelection;

class Client extends PassportClient implements CanHaveCalendars, CanHaveEvents, CanHaveArticles, CanNotify
{
    use HasHiddenData, HasSelection, HasUuid;

    public $incrementing = false;

    protected $fillable = [
        'user_id', 'asso_id', 'name', 'secret', 'redirect', 'personal_access_client', 'password_client', 'revoked', 'created_at', 'updated_at', 'scopes'
    ];

    protected $selection = [
        'paginate' => null,
		'filter' => [],
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

	public function isCalendarAccessibleBy(string $user_id): bool {
		return $this->asso()->currentMembers->wherePivot('user_id', $user_id)->exists();
	}

	public function isCalendarManageableBy(string $user_id): bool {
		return $this->asso()->hasOneRole('developer', ['user_id' => $user_id]);
	}

	public function isEventAccessibleBy(string $user_id): bool {
		return $this->asso()->currentMembers->wherePivot('user_id', $user_id)->exists();
	}

	public function isEventManageableBy(string $user_id): bool {
		return $this->asso()->hasOneRole('developer', ['user_id' => $user_id]);
	}

	public function isArticleAccessibleBy(string $user_id): bool {
		return $this->asso()->currentMembers->wherePivot('user_id', $user_id)->exists();
	}

	public function isArticleManageableBy(string $user_id): bool {
		return $this->asso()->hasOneRole('developer', ['user_id' => $user_id]);
	}
}
