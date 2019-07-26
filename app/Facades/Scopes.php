<?php
/**
 * Facade handling scopes and token system.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Scopes extends Facade
{
    /**
     * Save the accessor name.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Scopes';
    }
}
