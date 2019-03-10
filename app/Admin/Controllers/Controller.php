<?php
/**
 * Controlleur de base pour l'admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Par défaut l'accès est bloqué pour les superadmins.
     */
    public function __construct() {
        $this->middleware('role:superadmin');
    }
}
