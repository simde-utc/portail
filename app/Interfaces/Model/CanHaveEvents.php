<?php
/**
 * Indique que le modèle peut posséder des événements.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanHaveEvents
{
    /**
     * Renvoie la liste des événements.
     *
     * @return MorphMany
     */
    public function events();

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les événements appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isEventManageableBy(string $user_id): bool;
}
