<?php

namespace App\Interfaces;

Interface CanHaveCalendars {
    /**
     * Permet d'indiquer si la personne à le droit de voir les calendriers appartenant au modèle
     * @return boolean
     */
    public function isCalendarAccessibleBy(int $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les calendriers appartenant au modèle
     * @return boolean
     */
    public function isCalendarManageableBy(int $user_id): bool;
}
