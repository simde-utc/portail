<?php
/**
 * Façade permettant de communiquer avec Ginger
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Ginger extends Facade
{
    /**
     * Enregistre le nom de l'accesseur.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Ginger';
    }
}
