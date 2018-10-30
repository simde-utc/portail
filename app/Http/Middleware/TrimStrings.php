<?php
/**
 * Middleware par défaut de Laravel.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * Les champs qui ne doivent pas être touchés.
     *
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation',
    ];
}
