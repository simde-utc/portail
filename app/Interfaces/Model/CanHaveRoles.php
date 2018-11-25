<?php
/**
 * Indique que le modèle peut posséder des rôles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanHaveRoles
{
    /**
     * Renvoie la liste des roles.
     *
     * @return MorphMany
     */
    public function roles();

    /**
     * Permet d'indiquer si la personne à le droit de voir les rôles appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isRoleAccessibleBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les rôles appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isRoleManageableBy(string $user_id): bool;
}
