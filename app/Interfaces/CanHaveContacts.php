<?php

namespace App\Interfaces;

Interface CanHaveContacts {
    /**
     * Renvoie la liste des évènements
     * @return MorphMany
     */
    public function contacts();
}
