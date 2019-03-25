<?php
/**
 * Ajoute au controlleur un accès aux contacts.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;

trait HasContacts
{
    /**
     * Vérifie les droits du token.
     *
     * @param  Request $request
     * @param  string  $verb
     * @return void
     */
    protected function checkTokenRights(Request $request, string $verb='get')
    {
        $category = \ModelResolver::getCategoryFromObject($request->resource);

        if (!\Scopes::hasOne($request, \Scopes::getTokenType($request).'-'.$verb.'-contacts-'.$category)) {
            abort(503, 'L\'application n\'a pas le droit de voir les contacts de cette ressource');
        }
    }

    /**
     * Récupère un contact.
     *
     * @param  Request $request
     * @param  string  $verb
     * @return mixed
     */
    public function getContact(Request $request, string $verb='get')
    {
        $this->checkTokenRights($request, $verb);
        $contact = $request->resource->contacts()->where('id', $request->contact)->firstSelection();

        if ($contact) {
            if (\Auth::id()) {
                if ($verb !== 'get' && !$request->resource->isContactManageableBy(\Auth::id())) {
                    abort(503, 'Il n\'est pas possible à l\'utilisateur de gérer un contact pour cette ressource');
                }
            }

            return $contact;
        } else {
            abort(404, "Ce contact n'existe pas pour cette ressource");
        }
    }
}
