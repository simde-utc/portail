<?php
/**
 * Indique que le modèle peut posséder des calendriers.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanHaveCalendars
{
    /**
     * Renvoie la liste des calendriers.
     *
     * @return MorphMany
     */
    public function calendars();

    /**
     * Permet d'indiquer si la personne à le droit de voir les calendriers appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isCalendarAccessibleBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les calendriers appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isCalendarManageableBy(string $user_id): bool;
}
