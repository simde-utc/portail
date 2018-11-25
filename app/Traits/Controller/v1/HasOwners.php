<?php
/**
 * Ajoute au controlleur un gestion des possésseurs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;

trait HasOwners
{
    use HasMorphs;

    /**
     * Récupère le possésseur.
     *
     * @param  Request $request
     * @param  string  $modelName
     * @param  string  $modelText
     * @param  string  $verb
     * @return mixed
     */
    protected function getOwner(Request $request, string $modelName, string $modelText, string $verb='create')
    {
        return $this->getMorph($request, $modelName, $modelText, $verb, 'owned');
    }
}
