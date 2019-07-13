<?php
/**
 * Adds to the controller an access to the articles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits;

use Illuminate\Http\Request;
use Grimzy\LaravelMysqlSpatial\Types\Point;

trait HasPosition
{
    /**
     * Indicates that the user is a member a given instance.
     *
     * @param  Request $request
     * @return Point|null
     */
    public function getPosition(Request $request)
    {
        if ($request->filled('latitude') && $request->filled('longitude')) {
            return new Point($request->input('latitude'), $request->input('longitude'));
        }

        return null;
    }
}
