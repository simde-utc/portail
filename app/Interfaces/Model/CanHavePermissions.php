<?php
/**
 * Indique que le modèle peut posséder des permissions.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

interface CanHavePermissions
{
    /**
     * Renvoie la liste des roles.
     *
     * @return MorphMany
     */
    public function permissions();

    /**
     * Permet d'indiquer si la personne à le droit de voir les permissions appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isPermissionAccessibleBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les permissions appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isPermissionManageableBy(string $user_id): bool;
}
