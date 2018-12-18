<?php
/**
 * Modèle correspondant aux admins.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Support\Collection;
use App\Models\User;
use App\Traits\Model\HasHiddenData;

class Admin extends Administrator
{
    use HasHiddenData;

    protected $table = 'users';

    protected static $isAdministrator = [];
    protected static $permissions = [];

    public $incrementing = false;

    protected $fillable = [
        'firstname', 'lastname', 'email', 'image', 'is_active', 'last_login_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'name',
    ];

    protected $hidden = [
        'remember_token',
    ];

    /**
     * Retourne l'utilisateur associé.
     *
     * @return User
     */
    public function getUser()
    {
        return User::find($this->getKey());
    }

    /**
     * Donne le username.
     *
     * @return string
     */
    public function getUsernameAttribute()
    {
        return $this->email;
    }

    /**
     * Créer l'attribut name à la volée (concaténation du prénom et du nom).
     *
     * @return string
     */
    public function getNameAttribute()
    {
        if ($this->isActive()) {
            return $this->firstname.' '.strtoupper($this->lastname);
        } else {
            return 'Compte invité';
        }
    }

    /**
     * Get avatar attribute.
     *
     * @param mixed $avatar
     *
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        return $this->image;
    }

    /**
     * Indique si l'utilsateur est actif (sous-entendu, qu'il s'est bien déjà connecté une fois).
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->is_active === null || ((bool) $this->is_active) === true;
    }

    /**
     * Get all permissions of user.
     *
     * @return Collection
     */
    public function allPermissions(): Collection
    {
        $permission_ids = $this->getUser()->getUserPermissions()->pluck('id');

        return Permission::whereIn('id', $permission_ids)->get();
    }

    /**
     * Check if user has permission.
     *
     * @param string $permission
     *
     * @return boolean
     */
    public function can(string $permission): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        if (isset(static::$permissions[$this->id][$permission])) {
            return static::$permissions[$this->id][$permission];
        }

        return static::$permissions[$this->id][$permission] = $this->getUser()->hasOnePermission($permission);
    }

    /**
     * Check if user is administrator.
     *
     * @return boolean
     */
    public function isAdministrator(): bool
    {
        if (isset(static::$isAdministrator[$this->id])) {
            return static::$isAdministrator[$this->id];
        }

        return static::$isAdministrator[$this->id] = $this->getUser()->hasOneRole('superadmin');
    }

    /**
     * Check if user is $role.
     *
     * @param string $role
     *
     * @return boolean
     */
    public function isRole(string $role) : bool
    {
        return $this->getUser()->hasOneRole($role);
    }

    /**
     * Check if user in $roles.
     *
     * @param array $roles
     *
     * @return boolean
     */
    public function inRoles(array $roles=[]) : bool
    {
        return $this->getUser()->hasOneRole($roles);
    }

    /**
     * If visible for roles.
     *
     * @param array $roles
     *
     * @return boolean
     */
    public function visible(array $roles=[]) : bool
    {
        if (empty($roles)) {
            return true;
        }

        return $this->inRoles($roles) || $this->isAdministrator();
    }
}
