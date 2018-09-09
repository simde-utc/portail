<?php

namespace App\Interfaces\Model;

Interface CanHaveRoles {
    /**
     * Renvoie la liste des roles
     * @return MorphMany
     */
    public function roles();
}
