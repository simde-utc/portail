<?php
/**
 * Add the controller an access to creators, owners and validators of the resource.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

trait HasCreatorsAndOwnersAndValidators
{
    use HasCreatorsAndOwners;

    /**
     * Retrieve the validator.
     *
     * @param  Request $request
     * @param  Model   $owner
     * @param  string  $modelName
     * @param  string  $modelText
     * @param  string  $verb
     * @return mixed
     */
    protected function getValidatorFromOwner(Request $request, Model $owner, string $modelName, string $modelText,
        string $verb='create')
    {
        $tokenType = \Scopes::getTokenType($request);
        $client = \Scopes::getClient($request);
        $type = \ModelResolver::getName($owner);
        $scopeBegin = $tokenType.'-'.$verb.'-'.$modelName;

        if ($request->input('validated_by_type') === 'user'
            && \Auth::id()
            && $request->input('validated_by_id', \Auth::id()) === \Auth::id()
            && \Scopes::hasOne($request, [$scopeBegin.'s-'.$type.'s-validated-user', $scopeBegin.'s-users-validated'])) {
            $validator = \Auth::user();
        } else if ($request->input('validated_by_type') === 'client'
            && $request->input('validated_by_id', $client->id) === $client->id
            && \Scopes::hasOne($request, [$scopeBegin.'s-'.$type.'s-validated-client', $scopeBegin.'s-clients-validated'])) {
            $validator = $client;
        } else if ($request->input('validated_by_type') === 'asso'
            && $request->input('validated_by_id', $client->asso->id) === $client->asso->id
            && \Scopes::hasOne($request, [$scopeBegin.'s-'.$type.'s-validated-asso', $scopeBegin.'s-assos-validated'])) {
            $validator = \Scopes::getClient($request)->asso;
        } else {
            $validator = $this->getMorph($request, $modelName, $modelText, $verb, 'validated');
        }

        if (!$owner->{'is'.ucfirst($modelName).'ValidableBy'}($validator)) {
            abort(403, 'Le valideur n\'est pas autorisé à valider cette réservation');
        }

        return $validator;
    }
}
