<?php
/**
 * Add the controller an access to Creators and owners of the resource.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

trait HasCreatorsAndOwners
{
    use HasOwners;

    /**
     * The creator can be have several types: current user, association or client?
     *
     * @param  Request $request
     * @param  Model   $owner
     * @param  string  $modelName
     * @param  string  $modelText
     * @param  string  $verb
     * @return mixed
     */
    protected function getCreatorFromOwner(Request $request, Model $owner, string $modelName, string $modelText,
        string $verb='create')
    {
        if (\Scopes::isTransient($request)) {
            return \Auth::user();
        }

        $tokenType = \Scopes::getTokenType($request);
        $client = \Scopes::getClient($request);
        $type = \ModelResolver::getNameFromObject($owner);
        $scopeBegin = $tokenType.'-'.$verb.'-'.$modelName;

        if ($request->input('created_by_type', 'user') === 'user'
            && \Auth::id()
            && $request->input('created_by_id', \Auth::id()) === \Auth::id()
            && \Scopes::hasOne($request, [$scopeBegin.'s-'.$type.'s-owned-user', $scopeBegin.'s-users-created'])) {
            $creater = \Auth::user();
        } else if ($request->input('created_by_type', 'client') === 'client'
            && $request->input('created_by_id', $client->id) === $client->id
            && \Scopes::hasOne($request, [$scopeBegin.'s-'.$type.'s-owned-client', $scopeBegin.'s-clients-created'])) {
            $creater = $client;
        } else if ($request->input('created_by_type') === 'asso'
            && $request->input('created_by_id', $client->asso->id) === $client->asso->id
            && \Scopes::hasOne($request, [$scopeBegin.'s-'.$type.'s-owned-asso', $scopeBegin.'s-assos-created'])) {
            $creater = $client->asso;
        } else {
            $creater = $this->getMorph($request, $modelName, $modelText, $verb, 'created');
        }

        return $creater;
    }
}
