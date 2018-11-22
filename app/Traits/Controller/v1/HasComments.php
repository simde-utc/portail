<?php
/**
 * Ajoute au controlleur un accès aux commentaires.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;

trait HasComments
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
        $category = \ModelResolver::getCategory($request->resource);

        if (!\Scopes::hasOne($request, \Scopes::getTokenType($request).'-'.$verb.'-comments-'.$category)) {
            abort(503, 'L\'application n\'a pas le droit de voir les commentaires de cette ressource');
        }
    }

    /**
     * Récupère un commentaire.
     *
     * @param  Request $request
     * @param  string  $verb
     * @return mixed
     */
    public function getComment(Request $request, string $verb='get')
    {
        $this->checkTokenRights($request, $verb);
        $comment = $request->resource->comments()->where('id', $request->comment)->first();

        if ($comment) {
            if (\Auth::id()) {
                if (!$request->resource->isCommentAccessibleBy(\Auth::id())) {
                    abort(503, 'Vous n\'avez pas le droit de voir ce commentaire');
                }

                switch ($verb) {
                    case 'edit':
                        if (!$comment->created_by->isCommentEditableBy(\Auth::id())) {
                            abort(503, 'Il n\'est pas possible à l\'utilisateur de modifier ce commentaire \
                                pour cette ressource');
                        }
                        break;

                    case 'remove':
                        if (!$comment->created_by->isCommentDeletableBy(\Auth::id())) {
                            abort(503, 'Il n\'est pas possible à l\'utilisateur de modifier ce commentaire \
                                pour cette ressource');
                        }
                        break;

                    default:
                        break;
                }
            }

            return $comment;
        } else {
            abort(404, "Ce commentaire n'existe pas pour cette ressource");
        }
    }
}
