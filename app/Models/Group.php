<?php
/**
 * Model corresponding to aux groupes.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Cog\Contracts\Ownership\CanBeOwner;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Model\{
    HasMembers, HasDeletedSelection, HasVisibilitySelection
};
use App\Interfaces\Model\{
    CanHaveEvents, CanHaveCalendars, CanHaveContacts, CanHaveArticles, CanHaveRoles, CanHavePermissions
};
use App\Models\{
    User, Role
};

class Group extends Model implements CanBeOwner, CanHaveCalendars, CanHaveEvents, CanHaveContacts, CanHaveArticles,
    CanHaveRoles, CanHavePermissions
{
    use SoftDeletes, HasMembers, HasDeletedSelection, HasVisibilitySelection;

    protected $roleRelationTable = 'groups_members';

    protected $fillable = [
        'name', 'user_id', 'icon', 'visibility_id',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $hidden = [
        'user_id', 'visibility_id',
    ];

    protected $must = [
        'icon', 'visibility'
    ];

    protected $selection = [
        'visibilities' => '*',
        'order' => 'latest',
        'paginate' => 50,
        'day' => null,
        'week' => null,
        'month' => null,
        'year' => null,
        'filter' => [],
        'deleted' => 'without'
    ];

    /**
     * Appelé dès la création du modèle.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->assignRoles('group admin', [
                'user_id' => $model->user_id,
                'validated_by_id' => $model->user_id,
                'semester_id' => 0,
            ], true);
        });
    }

    /**
     * Specific scope to have only the private resources.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopePrivateVisibility(Builder $query)
    {
        $visibility = $this->getSelectionForVisibility('private');
        $user = $this->getUserForVisibility();

        if ($user) {
            $group_ids = $user->groups()->pluck('id')->toArray();

            return $query->where('visibility_id', $visibility->id)->whereIn('id', $group_ids);
        }
    }

    /**
     * Relation with the group owner.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation with the group owner.
     *
     * @return mixed
     */
    public function owner()
    {
        return $this->user();
    }

    /**
     * Relation with the visibility.
     *
     * @return mixed
     */
    public function visibility()
    {
        return $this->belongsTo(Visibility::class, 'visibility_id');
    }

    /**
     * Indicates if its possible to delete a role depending on a given group id.
     * By default a group is not removable if it has already be assigned.
     * But we allow its deletion if it's assigned to one and only groupe.
     *
     * @param  Role   $role
     * @param  string $group_id
     * @return boolean
     */
    public function isRoleForIdDeletable(Role $role, string $group_id)
    {
        return true;
    }

    /**
     * Indicates if a role is accessible.
     * Only by members for custom roles of the assciation. Otherwise always visible.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isRoleAccessibleBy(string $user_id): bool
    {
        if ($this->id) {
            return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
        } else {
            return true;
        }
    }

    /**
     * Indicates if a role is manageable.
     * Only by members for custom roles of the assciation. Otherwise always visible.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isRoleManageableBy(string $user_id): bool
    {
        if ($this->id) {
            return $this->hasOnePermission('role', [
                'user_id' => $user_id,
            ]);
        } else {
            return User::find($user_id)->hasOnePermission('role');
        }
    }

    /**
     * Indicates if a permission is accessible.
     * Only by members for custom permissions of the assciation. Otherwise always visible.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isPermissionAccessibleBy(string $user_id): bool
    {
        if ($this->id) {
            return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
        } else {
            return true;
        }
    }

    /**
     * Indicates if a permission is manageable.
     * Only by members for custom permissions of the assciation. Otherwise always visible.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isPermissionManageableBy(string $user_id): bool
    {
        if ($this->id) {
            return $this->hasOnePermission('permission', [
                'user_id' => $user_id,
            ]);
        } else {
            return User::find($user_id)->hasOnePermission('permission');
        }
    }

    /**
     * Relation with contact means.
     *
     * @return mixed
     */
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'owned_by');
    }

    /**
     * Indicates if the contact mean est accessible.
     * Only by group members.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isContactAccessibleBy(string $user_id): bool
    {
        return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
    }

    /**
     * Indicates if the contact mean is manageable.
     * Only by group members.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isContactManageableBy(string $user_id): bool
    {
        return $this->hasOnePermission('contact', [
            'user_id' => $user_id,
        ]);
    }

    /**
     * Relation with calendars.
     *
     * @return mixed
     */
    public function calendars()
    {
        return $this->morphMany(Calendar::class, 'owned_by');
    }

    /**
     * Indicates if the private calendar is manageable.
     * Only by group members.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCalendarManageableBy(string $user_id): bool
    {
        return $this->hasOnePermission('calendar', [
            'user_id' => $user_id,
        ]);
    }

    /**
     * Relation with events.
     *
     * @return mixed
     */
    public function events()
    {
        return $this->morphMany(Event::class, 'owned_by');
    }

    /**
     * Indicates if an private event is manageable.
     * Only by group members.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isEventManageableBy(string $user_id): bool
    {
        return $this->hasOnePermission('event', [
            'user_id' => $user_id,
        ]);
    }

    /**
     * Relation with articles.
     *
     * @return mixed
     */
    public function articles()
    {
        return $this->morphMany(Article::class, 'owned_by');
    }

    /**
     * Indicates if the private article is manageable.
     * Only by group members.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isArticleManageableBy(string $user_id): bool
    {
        return $this->hasOnePermission('article', [
            'user_id' => $user_id,
        ]);
    }
}
