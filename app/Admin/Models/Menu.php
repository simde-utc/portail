<?php
/**
 * Modèle correspondant aux admins.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Models;

use Encore\Admin\Auth\Database\Menu as BaseMenu;

class Menu extends BaseMenu
{
    public static function create($args)
    {
        if (in_array('title', $args)) {
            $menu = self::where('title', $args['title'])->first();

            if ($menu) {
                return $menu;
            }
        }

        return BaseMenu::create($args);
    }
}
