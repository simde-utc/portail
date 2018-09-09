<?php

namespace App\Interfaces\Model;

Interface CanHavePermissions {
    /**
     * Renvoie la liste des roles
     * @return MorphMany
     */
    public function permissions();
}
