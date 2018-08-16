<?php

namespace App\Models;

use Cog\Contracts\Ownership\CanBeOwner;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Model\HasMembers;
use App\Interfaces\Model\CanHaveEvents;
use App\Interfaces\Model\CanHaveCalendars;
use App\Interfaces\Model\CanHaveContacts;
use App\Interfaces\Model\CanHaveArticles;

class Group extends Model implements CanBeOwner, CanHaveCalendars, CanHaveEvents, CanHaveContacts, CanHaveArticles
{
    use SoftDeletes, HasMembers;

    public static function boot() {
        parent::boot();
        
        static::created(function ($model) {
            $model->assignRoles('group admin', [
				'user_id' => $model->user_id,
				'validated_by' => $model->user_id,
				'semester_id' => 0,
			], true);
        });
    }

	protected $roleRelationTable = 'groups_members';

    protected $fillable = [
        'name', 'user_id', 'icon_id', 'visibility_id', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $hidden = [
        'user_id', 'visibility_id',
    ];

    protected $must = [
        'icon_id'
    ];

    protected $selection = [
        'order' => 'latest',
        'paginate' => 50,
        'day' => null,
        'week' => null,
        'month' => null,
        'year' => null,
    ];

    public function owner() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function visibility() {
    	return $this->belongsTo(Visibility::class, 'visibility_id');
    }

	// Par défaut, un role n'est pas supprimable s'il a déjà été assigné
    // Mais on permet sa suppression s'il est assigné à un seul groupe
	public function isRoleForIdDeletable($role, $id) {
		return true;
	}

    public function contacts() {
    	return $this->morphMany(Contact::class, 'owned_by');
    }

	public function isContactAccessibleBy(string $user_id): bool {
		return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
	}

	public function isContactManageableBy(string $user_id): bool {
		return $this->hasOnePermission('group_contact', [
			'user_id' => $user_id,
		]);
	}

    public function calendars() {
    	return $this->morphMany(Calendar::class, 'owned_by');
    }

	public function isCalendarAccessibleBy(string $user_id): bool {
		return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
	}

	public function isCalendarManageableBy(string $user_id): bool {
		return $this->hasOnePermission('group_calendar', [
			'user_id' => $user_id,
		]);
	}

    public function events() {
    	return $this->morphMany(Event::class, 'owned_by');
    }

	public function isEventAccessibleBy(string $user_id): bool {
		return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
	}

	public function isEventManageableBy(string $user_id): bool {
		return $this->hasOnePermission('group_event', [
			'user_id' => $user_id,
		]);
	}

    public function articles() {
    	return $this->morphMany(Article::class, 'owned_by');
    }

	public function isArticleAccessibleBy(string $user_id): bool {
		return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
	}

	public function isArticleManageableBy(string $user_id): bool {
		return $this->hasOnePermission('group_article', [
			'user_id' => $user_id,
		]);
	}
}
