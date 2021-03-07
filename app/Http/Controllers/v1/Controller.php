<?php
/**
 * V1 controller.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Noé Amiot <noe.amiot@etu.utc.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1;

use App\Models\User;
use App\Models\Asso;
use App\Models\Group;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use App\Exceptions\PortailException;
use Illuminate\Support\Collection;
use App\Traits\Controller\v1\HasBulkMethods;

class Controller extends BaseController
{
    use HasBulkMethods;

    /**
     * Know what to display.
     * TODO: deprecate for official v1.
     * TODO: transform into Trait working with getSelection.
     *
     * @param  Request $request
     * @param  array   $choices
     * @param  array   $defaultChoices
     * @return array
     * @throws PortailException For bad $choices.
     */
    protected function getChoices(Request $request, array $choices, array $defaultChoices=[])
    {
        $only = $request->input('only') ? explode(',', $request->input('only')) : [];
        $except = $request->input('except') ? explode(',', $request->input('except')) : [];

        if (count(array_intersect($only, $choices)) !== count($only)
         || count(array_intersect($except, $choices)) !== count($except)) {
            throw new PortailException('Il n\'est possible de spécifier uniquement: '.implode(', ', $choices));
        }

        if (empty($only) && empty($except)) {
            return $defaultChoices;
        }

        if (count($only) > 0) {
            $choices = $only;
        }

        foreach ($except as $choice) {
            if (($key = array_search($choice, $choices)) !== false) {
                unset($choices[$key]);
            }
        }

        return $choices;
    }
}
