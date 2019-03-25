<?php
/**
 * Modèle correspondant aux groupes.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

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
        'icon'
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
     * Scope spécifique pour n'avoir que les ressources privées.
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
     * Relation avec le possédeur du groupe.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec le possédeur du groupe.
     *
     * @return mixed
     */
    public function owner()
    {
        return $this->user();
    }

    /**
     * Relation avec la visibilité.
     *
     * @return mixed
     */
    public function visibility()
    {
        return $this->belongsTo(Visibility::class, 'visibility_id');
    }

    /**
     * Indique s'il est possible de supprimer un rôle pour le groupe d'un id précis.
     * Par défaut, un role n'est pas supprimable s'il a déjà été assigné.
     * Mais on permet sa suppression s'il est assigné à un seul groupe.
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
     * Indique si un rôle est accessible.
     * Uniquement par les membres pour les rôles customs de l'associations sinon toujours visibles
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
     * Indique si un rôle est gérable.
     * Uniquement par les membres pour les rôles customs de l'associations sinon toujours visibles
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
     * Indique si une permission est accessible.
     * Uniquement par les membres pour les permissions customs de l'associations sinon toujours visibles
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
     * Indique si une permission est gérable.
     * Uniquement par les membres pour les permissions customs de l'associations sinon toujours visibles
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
     * Relation avec les moyens de contact.
     *
     * @return mixed
     */
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'owned_by');
    }

    /**
     * Indique si le moyen de contact est accessible.
     * Uniquement par les membres du groupe.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isContactAccessibleBy(string $user_id): bool
    {
        return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
    }

    /**
     * Indique si le moyen de contact est gérable.
     * Uniquement par les membres du groupe.
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
     * Relation avec les calendriers.
     *
     * @return mixed
     */
    public function calendars()
    {
        return $this->morphMany(Calendar::class, 'owned_by');
    }

    /**
     * Indique si le calendrier privé est gérable.
     * Uniquement par les membres du groupe.
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
     * Relation avec les événements.
     *
     * @return mixed
     */
    public function events()
    {
        return $this->morphMany(Event::class, 'owned_by');
    }

    /**
     * Indique si l'événement privé est gérable.
     * Uniquement par les membres du groupe.
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
     * Relation avec les articles.
     *
     * @return mixed
     */
    public function articles()
    {
        return $this->morphMany(Article::class, 'owned_by');
    }

    /**
     * Indique si l'article privé est gérable.
     * Uniquement par les membres du groupe.
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
