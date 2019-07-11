<?php
/**
 * Adds the controller an access to resource Creators.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;

trait HasCreators
{
    use HasMorphs;

    /**
     * The creator can be have several types: current user, association or client?
     *
     * @param  Request $request
     * @param  string  $modelName
     * @param  string  $modelText
     * @param  string  $verb
     * @return mixed
     */
    protected function getCreater(Request $request, string $modelName, string $modelText, string $verb='create')
    {
        return $this->getMorph($request, $modelName, $modelText, $verb, 'created');
    }
}
