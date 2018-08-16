<?php

namespace App\Interfaces\Model;

Interface CanHaveCalendars {
    /**
     * Renvoie la liste des calendriers
     * @return MorphMany
     */
    public function calendars();

    /**
     * Permet d'indiquer si la personne à le droit de voir les calendriers appartenant au modèle
     * @return boolean
     */
    public function isCalendarAccessibleBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les calendriers appartenant au modèle
     * @return boolean
     */
    public function isCalendarManageableBy(string $user_id): bool;
}
