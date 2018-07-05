<?php

namespace App\Interfaces;

Interface CanHaveEvents {
    /**
     * Permet d'indiquer si la personne à le droit de modifier les évènements appartenant au modèle
     * @return boolean
     */
    public function isEventAccessibleBy(int $user_id): bool;
}
