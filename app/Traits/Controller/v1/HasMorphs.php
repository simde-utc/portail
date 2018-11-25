<?php
/**
 * Ajoute au controlleur une gestion des morphs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;

trait HasMorphs
{
    /**
     * Récupère un morph à partir de la requête.
     *
     * @param  Request $request
     * @param  string  $modelName
     * @param  string  $modelText
     * @param  string  $verb
     * @param  string  $type
     * @param  string  $action
     * @return mixed
     */
    protected function getMorph(Request $request, string $modelName, string $modelText, string $verb='create',
        string $type='created', string $action='ManageableBy')
    {
        $scopeHead = \Scopes::getTokenType($request);
        $interface = '\App\Interfaces\Model\CanHave'.ucfirst($modelName).'s';

        if ($request->filled($type.'_by_type')) {
            if ($request->filled($type.'_by_id')) {
                $morph = \ModelResolver::getModel($request->input($type.'_by_type'))->find($request->input($type.'_by_id'));

                if (!($morph instanceof $interface)) {
                    abort(400, 'L\'instance n\'a pas le droit de posséder de '.$modelName.'s');
                }

                if (\Auth::id() && !$morph->{'is'.ucfirst($modelName).$action}(\Auth::id())) {
                    abort(403, 'L\'utilisateur n\'a pas les droits suffisants');
                }
            } else {
                if ($request->input($type.'_by_type', 'client') === 'client') {
                    $morph = \Scopes::getClient($request);
                } else if ($request->input($type.'_by_type', 'client') === 'asso') {
                    $morph = \Scopes::getClient($request)->asso;
                } else {
                    $morph = \Scopes::isClientToken($request) ? \Scopes::getClient($request) : \Auth::user();
                }

                if (!($morph instanceof $interface)) {
                    abort(400, 'L\'instance n\'a pas le droit de posséder de '.$modelName.'s');
                }
            }
        } else {
            return null;
        }

        $scope = $scopeHead.'-'.$verb.'-'.$modelName.'s-'.$request->input($type.'_by_type', $scopeHead).'s-'.$type;

        if ($type === 'owned') {
            $requestType = $request->input($type.'_by_type');
            $scope = array_keys(\Scopes::getRelatives($scopeHead.'-'.$verb.'-'.$modelName.'s-'.$requestType.'s-'.$type));
        }

        if (!\Scopes::hasOne($request, $scope)) {
            abort(403, 'Il ne vous est pas autorisé de créer de '.$modelName.'s');
        }

        return $morph;
    }
}
