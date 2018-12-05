<?php
/**
 * Modèle correspondant aux admins.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Encore\Admin\Auth\Database\HasPermissions;
use Illuminate\Auth\Authenticatable;
use Encore\Admin\Traits\AdminBuilder;
use Illuminate\Support\Collection;

class Admin extends Model
{
    use Authenticatable, HasPermissions, AdminBuilder;

    protected $table = 'users';

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
    public function getUser() {
        return User::find($this->getKey());
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
        return $this->getUser()->getUserPermissions();
    }

    /**
     * Check if user has permission.
     *
     * @param string $permission
     *
     * @return bool
     */
    public function can(string $permission): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        return $this->getUser()->hasOnePermission($permission);
    }

    /**
     * Check if user is administrator.
     *
     * @return bool
     */
    public function isAdministrator(): bool
    {
        return $this->getUser()->hasOneRole('admin');
    }

    /**
     * Check if user is $role.
     *
     * @param string $role
     *
     * @return bool
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
     * @return bool
     */
    public function inRoles(array $roles = []) : bool
    {
        return $this->getUser()->hasOneRole($roles);
    }

    /**
     * If visible for roles.
     *
     * @param array $roles
     *
     * @return bool
     */
    public function visible(array $roles = []) : bool
    {
        if (empty($roles)) {
            return true;
        }

        return $this->inRoles($roles) || $this->isAdministrator();
    }
}
