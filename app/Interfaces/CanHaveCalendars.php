<?php

namespace App\Interfaces;

Interface CanHaveCalendars {
    /**
     * Permet d'indiquer si la personne à le droit de modifier les calendriers appartenant au modèle
     * @return boolean
     */
    public function isCalendarAccessibleBy(int $user_id): bool;

    public function isCalendarManageableBy(int $user_id): bool;
}
