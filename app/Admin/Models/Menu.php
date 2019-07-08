<?php
/**
 * Admin menus model.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Models;

use Encore\Admin\Auth\Database\Menu as BaseMenu;
use Illuminate\Support\Facades\DB;

class Menu extends BaseMenu
{
    /**
     * Define the order automatically.
     *
     * @param  array $args
     * @return BaseMenu
     */
    public static function create(array $args)
    {
        $menu = static::orderBy('order', 'DESC')->first();

        $args['order'] = (($menu ? $menu->order : 0) + 1);

        return BaseMenu::create($args);
    }

    /**
     * Returns all menus in the form of nodes
     *
     * @return array
     */
    public function allNodes() : array
    {
        $connection = config('admin.database.connection') ?: config('database.default');
        $orderColumn = DB::connection($connection)->getQueryGrammar()->wrap($this->orderColumn);

        $byOrder = $orderColumn.' = 0,'.$orderColumn;

        return static::orderByRaw($byOrder)->get()->toArray();
    }
}
