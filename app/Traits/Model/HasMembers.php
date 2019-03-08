<?php
/**
 * Ajoute une gestion des membres.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

use App\Exceptions\PortailException;
use App\Traits\Model\HasRoles;
use Illuminate\Support\Collection;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Permission;
use App\Models\Visibility;
use App\Models\User;

trait HasMembers
{
    use HasRoles;

    /**
     * Méthode appelée au chargement du class.
     *
     * @return void
     */
    public static function bootHasMembers()
    {
        // Si on souhaite supprimer la ressource, on supprime les membres associés.
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->members()->detach();
        });
    }

    /**
     * Récupération du nom de la table de relation.
     *
     * @return string
     */
    protected function getMemberRelationTable()
    {
        return ($this->memberRelationTable ?? $this->getTable().'_members');
    }

    /**
     * Liste des membres (validés par une personne).
     *
     * @return mixed
     */
    public function allMembers()
    {
        return $this->belongsToMany(User::class, $this->getMemberRelationTable())
            ->withPivot('semester_id', 'role_id', 'validated_by_id', 'created_at', 'updated_at');
    }

    /**
     * Liste des membres de ce semestre ou d'aucun semestre (validés par une personne).
     *
     * @return mixed
     */
    public function currentAllMembers()
    {
        return $this->belongsToMany(User::class, $this->getMemberRelationTable())
            ->wherePivotIn('semester_id', [0, Semester::getThisSemester()->id])
            ->withPivot('semester_id', 'role_id', 'validated_by_id', 'created_at', 'updated_at');
    }

    /**
     * Liste des membres (validés par une personne).
     *
     * @return mixed
     */
    public function members()
    {
        return $this->belongsToMany(User::class, $this->getMemberRelationTable())
            ->whereNotNull('validated_by_id')
        ->withPivot('semester_id', 'role_id', 'validated_by_id', 'created_at', 'updated_at');
    }

    /**
     * Liste des membres de ce semestre ou d'aucun semestre (validés par une personne).
     *
     * @return mixed
     */
    public function currentMembers()
    {
        return $this->belongsToMany(User::class, $this->getMemberRelationTable())
            ->wherePivotIn('semester_id', [0, Semester::getThisSemester()->id])->whereNotNull('validated_by_id')
            ->withPivot('semester_id', 'role_id', 'validated_by_id', 'created_at', 'updated_at');
    }

    /**
     * Liste des personnes souhaitant se joindre (non validés).
     *
     * @return mixed
     */
    public function joiners()
    {
        return $this->belongsToMany(User::class, $this->getMemberRelationTable())->whereNull('validated_by_id')
            ->withPivot('semester_id', 'role_id', 'validated_by_id', 'created_at', 'updated_at');
    }

    /**
     * Liste des personnes souhaitant se joindre ce semestre ou d'aucun semestre (non validés).
     *
     * @return mixed
     */
    public function currentJoiners()
    {
        return $this->belongsToMany(User::class, $this->getMemberRelationTable())
            ->wherePivotIn('semester_id', [0, Semester::getThisSemester()->id])->whereNotNull('validated_by_id')
            ->withPivot('semester_id', 'role_id', 'validated_by_id', 'created_at', 'updated_at');
    }

    /**
     * Permet d'assigner un ou plusieurs membres en fonction des données fournis.
     *
     * @param  string|array|Collection $members
     * @param  array                   $data    Possibilité d'affecter role_id, semester_id, validated_by_id, user_id.
     * @param  boolean                 $force   Permet de sauter les sécurités d'ajout (à utiliser avec prudence).
     *
     * @return mixed
     */
    public function assignMembers($members, array $data=[], bool $force=false)
    {
        $data['semester_id'] = ($data['semester_id'] ?? Semester::getThisSemester()->id);

        if (!$force && $this->visibility_id !== null && $this->visibility_id >= Visibility::findByType('private')->id) {
            if (isset($data['validated_by_id'])) {
                $manageablePerms = $this->getUserPermissions($data['validated_by_id']);

                if (!$manageablePerms->contains('type', 'members')
                    && !$manageablePerms->contains('type', 'admin')) {
                    throw new PortailException('La personne demandant la validation n\'est pas habilitée à ajouter de membres, \
                        il s\'agit d\'un groupe fermé');
                }
            } else {
                throw new PortailException('L\'ajout de membre est fermé. Il est nécessaire qu\'une personne ayant les droits \
                    d\'ajout ajoute la personne');
            }
        }

        $members = User::getUsers(stringToArray($members));

        if (($data['role_id'] ?? false)) {
            $role = Role::find($data['role_id'], $this);

            if ($role === null) {
                throw new PortailException('Ce role ne peut-être assigné ou n\'existe pas');
            }

            if (!$force) {
                $manageableRoles = $this->getUserRoles(($data['validated_by_id'] ?? \Auth::id()));

                if (!$manageableRoles->contains('id', $data['role_id'])) {
                    throw new PortailException('La personne demandant l\'affectation de rôle n\'est pas habilitée à \
                        donner ce rôle: '.$role->name);
                }

                if ($this->roles()->wherePivot('role_id', $data['role_id'])
                    ->whereNotIn($this->getMemberRelationTable().'.user_id', $members->pluck('id'))->get()
                    ->count() > ($role->limited_at - $members->count())) {
                    throw new PortailException('Le nombre de personnes ayant ce role a été dépassé. \
                        Limité à '.$role->limited_at);
                }
            }
        }

        $addMembers = [];

        foreach ($members as $member) {
            $addMembers[($member->id ?? $member)] = $data;
        }

        try {
            $this->allMembers()->withTimestamps()->attach($addMembers);
        } catch (\Exception $e) {
            throw new PortailException('Déjà membre', 409);
        }

        return $this;
    }

    /**
     * Permet de modifier un ou plusieurs membres en fonction des données fournis.
     *
     * @param  string|array|Collection $members
     * @param  array                   $data        Possibilité d'utiliser role_id, semester_id, validated_by_id, user_id pour matcher un member ou plusieurs membres.
     * @param  array                   $updatedData Possibilité d'affecter role_id, semester_id, validated_by_id, user_id.
     * @param  boolean                 $force       Permet de sauter les sécurités d'ajout (à utiliser avec prudence).
     * @return mixed
     */
    public function updateMembers($members, array $data=[], array $updatedData=[], bool $force=false)
    {
        $data['semester_id'] = ($data['semester_id'] ?? Semester::getThisSemester()->id);
        $updatedData['semester_id'] = ($updatedData['semester_id'] ?? Semester::getThisSemester()->id);
        $members = User::getUsers(stringToArray($members));

        if (!$force) {
            if (($data['role_id'] ?? false)) {
                $role = Role::find($data['role_id'], $this);

                if ($role === null) {
                    throw new PortailException('Ce role ne peut-être assigné ou n\'existe pas');
                }

                if (isset($updatedData['validated_by_id']) || \Auth::id()) {
                    $manageableRoles = $this->getUserRoles(($updatedData['validated_by_id'] ?? \Auth::id()));

                    if (!$manageableRoles->contains('id', $role->id)) {
                        throw new PortailException('La personne demandant l\'affectation de rôle n\'est pas habilitée à \
                            modifier ce rôle: '.$role->name);
                    }
                }
            }

            if (($updatedData['role_id'] ?? false)) {
                $role = Role::find($updatedData['role_id'], $this);

                if ($role === null) {
                    throw new PortailException('Ce role ne peut-être assigné ou n\'existe pas');
                }

                if (isset($updatedData['validated_by_id']) || \Auth::id()) {
                    $manageableRoles = (
                        $manageableRoles ?? $this->getUserRoles(($updatedData['validated_by_id'] ?? \Auth::id()))
                    );

                    if (!$manageableRoles->contains('id', $role->id)) {
                        throw new PortailException('La personne demandant l\'affectation de rôle n\'est pas habilitée à \
                            modifier ce rôle: '.$role->name);
                    }

                    if ($this->roles()->wherePivot('role_id', $role->id)
                        ->whereNotIn($this->getMemberRelationTable().'.user_id', $members->pluck('id'))->get()
                        ->count() > ($role->limited_at - $members->count())) {
                        throw new PortailException('Le nombre de personnes ayant ce role a été dépassé. \
                            Limité à '.$role->limited_at);
                    }
                }
            }
        }

        $updatedMembers = [];

        foreach ($members as $member) {
            array_push($updatedMembers, ($member->id ?? $member));
        }

        $toUpdate = $this->allMembers()->withTimestamps();

        foreach ($data as $key => $value) {
            $toUpdate->wherePivot($key, $value);
        }

        try {
            foreach ($updatedMembers as $updatedMember) {
                $toUpdate->updateExistingPivot($updatedMember, $updatedData);
            }
        } catch (\Exception $e) {
            throw new PortailException('Les données d\'un membre ne peuvent être modifiées');
        }

        return $this;
    }

    /**
     * Permet de supprimer un ou plusieurs membres en fonction des données fournis.
     *
     * @param  string|array|Collection $members
     * @param  array                   $data       Possibilité d'utiliser role_id, semester_id, validated_by_id, user_id pour matcher un member ou plusieurs membres.
     * @param  string                  $removed_by Personne demandant la suppression.
     * @param  boolean                 $force      Permet de sauter les sécurités d'ajout (à utiliser avec prudence).
     * @return mixed
     */
    public function removeMembers($members, array $data=[], string $removed_by=null, bool $force=false)
    {
        $data['semester_id'] = ($data['semester_id'] ?? Semester::getThisSemester()->id);
        $members = User::getUsers(stringToArray($members));
        $removed_by = ($removed_by ?? \Auth::id());

        if (($data['role_id'] ?? false)) {
            $manageableRoles = $this->getUserRoles($removed_by);
            $manageablePerms = $this->getUserPermissions($removed_by);

            $role = Role::find($data['role_id'], $this);

            if ($role === null) {
                throw new PortailException('Ce role ne peut-être assigné ou n\'existe pas');
            }

            if (!$force && $removed_by !== null) {
                if (!$manageableRoles->contains('id', $data['role_id'])) {
                    throw new PortailException('La personne demandant la suppression du rôle n\'est pas habilitée \
                        à modifier ce rôle: '.$role->name);
                }
            }
        }

        $delMembers = [];

        foreach ($members as $member) {
            array_push($delMembers, ($member->id ?? $member));
        }

        $toDetach = $this->allMembers();

        foreach ($data as $key => $value) {
            $toDetach->wherePivot($key, $value);
        }

        try {
            $toDetach->detach($delMembers);
        } catch (\Exception $e) {
            throw new PortailException('Une erreur a été recontrée à la suppression d\'un membre');
        }

        return $this;
    }

    /**
     * Permet de synchroniser (tout supprimer et assigner de nouveaux) un ou plusieurs membres en fonction des données fournis.
     *
     * @param  string|array|Collection $members
     * @param  array                   $data       Possibilité d'utiliser role_id, semester_id, validated_by_id, user_id pour matcher un member ou plusieurs membres.
     * @param  string                  $removed_by Personne demandant la suppression.
     * @param  boolean                 $force      Permet de sauter les sécurités d'ajout (à utiliser avec prudence).
     * @return mixed
     */
    public function syncMembers($members, array $data=[], string $removed_by=null, bool $force=false)
    {
        $data['semester_id'] = ($data['semester_id'] ?? Semester::getThisSemester()->id);
        $currentMembers = $this->currentMembers->pluck('id');
        $members = User::getUsers(stringToArray($members))->pluck('id');
        $intersectedMembers = $currentMembers->intersect($members);

        $oldData = [];
        if (($data['semester_id'] ?? false)) {
            $oldData['semester_id'] = $data['semester_id'];
        }

        if (($data['role_id'] ?? false)) {
            $oldData['role_id'] = $data['role_id'];
        }

        return $this->assignMembers($members->diff($currentMembers), $data, $force)
            ->updateMembers($intersectedMembers, $oldData, $data, $force)
            ->removeMembers($currentMembers->diff($members), $data, $removed_by, $force);
    }

    /**
     * Regarde si un membre parmi la liste existe ou non.
     *
     * @param  string|array|Collection $members
     * @param  array                   $data    Possibilité d'utiliser role_id, semester_id, validated_by_id, user_id pour matcher un member ou plusieurs membres.
     * @return boolean
     */
    public function hasOneMember($members, array $data=[])
    {
        return $this->members()->wherePivotIn('semester_id', [0, ($data['semester_id'] ?? Semester::getThisSemester()->id)])
            ->wherePivotIn('user_id', User::getUsers(stringToArray($members))->pluck('id'))->get()->count() > 0;
    }

    /** Regarde si tous les membres parmi la liste existe ou non.
     *
     * @param  string|array|Collection $members
     * @param  array                   $data    Possibilité d'utiliser role_id, semester_id, validated_by_id, user_id pour matcher un member ou plusieurs membres.
     * @return boolean
     */
    public function hasAllMembers($members, array $data=[])
    {
        $user_ids = User::getUsers(stringToArray($members))->pluck('id');

        return $this->members()->wherePivotIn('semester_id', [0, ($data['semester_id'] ?? Semester::getThisSemester()->id)])
            ->wherePivotIn('user_id', $user_ids)->get()->count() > $user_ids->count();
    }
}
