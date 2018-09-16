<?php

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;

trait HasComments
{
	protected function checkTokenRights(Request $request, string $verb = 'get') {
		if (!\Scopes::hasOne($request, \Scopes::getTokenType($request).'-'.$verb.'-comments-'.\ModelResolver::getCategory($request->resource)))
			abort(503, 'L\'application n\'a pas le droit de voir les commentaires de cette ressource');
	}

	public function getComment(Request $request, string $verb = 'get') {
		$this->checkTokenRights($request, $verb);
		$comment = $request->resource->comments()->where('id', $request->comment)->first();

		if ($comment) {
			if (\Auth::id()) {
				if (!$request->resource->isCommentAccessibleBy(\Auth::id()))
					abort(503, 'Vous n\'avez pas le droit de voir ce commentaire');

				switch ($verb) {
					case 'edit':
						if (!$comment->created_by->isCommentEditableBy(\Auth::id()))
							abort(503, 'Il n\'est pas possible à l\'utilisateur de modifier ce commentaire pour cette ressource');
						break;

					case 'remove':
						if (!$comment->created_by->isCommentDeletableBy(\Auth::id()))
							abort(503, 'Il n\'est pas possible à l\'utilisateur de modifier ce commentaire pour cette ressource');
						break;

					default:
					break;
				}
			}

			return $comment;
		}
		else
			abort(404, "Ce commentaire n'existe pas pour cette ressource");
	}
}
