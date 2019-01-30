<?php
/**
 * Ajoute au controlleur un accès aux créateurs de la ressource.
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
     * Le créateur peut être multiple: le user, l'asso ou le client courant.
     *
     * @param  Request $request
     * @param  string  $modelName
     * @param  string  $modelText
     * @param  string  $verb
     * @return mixed
     */
    protected function getCreator(Request $request, string $modelName, string $modelText, string $verb='create')
    {
        return $this->getMorph($request, $modelName, $modelText, $verb, 'created');
    }
}
