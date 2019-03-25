<?php
/**
 * Indique que le modèle peut posséder des contacts.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanHaveContacts
{
    /**
     * Renvoie la liste des contacts.
     *
     * @return MorphMany
     */
    public function contacts();

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les contacts appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isContactManageableBy(string $user_id): bool;
}
