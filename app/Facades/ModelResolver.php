<?php
/**
 * Facade to retrieve models dynamically. 
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ModelResolver extends Facade
{
    /**
     * Saves the accessor name.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ModelResolver';
    }
}
