<?php

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\Event;
use App\Models\User;
use App\Models\Article;
use App\Models\Client;
use App\Models\Model;
use Illuminate\Http\Request;

trait HasArticles
{
	use HasEvents {
		HasEvents::isPrivate as protected isEventPrivate;
		HasEvents::tokenCanSee as protected tokenCanSeeEvent;
	}

	public function isPrivate($user_id, $model = null) {
		if ($model === null)
			return false;

		if ($model instanceof Article)
			return $model->owned_by->isArticleAccessibleBy($user_id);
		else
			return $this->isEventPrivate($user_id, $model);
    }

	protected function tokenCanSee(Request $request, Model $model, string $verb = 'get') {
		if ($model instanceof Article) {
			$scopeHead = \Scopes::getTokenType($request);

			if (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-articles-'.\ModelResolver::getName($model->owned_by_type).'s-owned'))
				return true;

			if (((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-articles-'.\ModelResolver::getName($model->owned_by_type).'s-owned-user'))
					&& \Auth::id()
					&& $model->created_by_type === User::class
					&& $model->created_by_id === \Auth::id())
				|| ((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-articles-'.\ModelResolver::getName($model->owned_by_type).'s-owned-client'))
					&& $model->created_by_type === Client::class
					&& $model->created_by_id === \Scopes::getClient($request)->id)
				|| ((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-articles-'.\ModelResolver::getName($model->owned_by_type).'s-owned-asso'))
					&& $model->created_by_type === Asso::class
					&& $model->created_by_id === \Scopes::getClient($request)->asso->id)) {
				if (\Scopes::isUserToken($request)) {
					$functionToCall = 'isArticle'.($verb === 'get' ? 'Accessible' : 'Manageable').'By';

					if ($model->owned_by->$functionToCall(\Auth::id()))
						return true;
				}
				else
					return true;
			}

			return \Scopes::hasOne($request, $scopeHead.'-'.$verb.'-articles-'.\ModelResolver::getName($model->owned_by_type).'s-created');
		}
		else
			return $this->tokenCanSeeEvent($request, $model, $verb);
	}

	protected function getArticle(Request $request, User $user = null, string $id, string $verb = 'get') {
		$article = Article::find($id);

		if ($article) {
			// On vérifie si l'accès est publique.
	        if (\Scopes::isOauthRequest($request)) {
				if (!$this->tokenCanSee($request, $article, $verb))
					abort(403, 'L\'application n\'a pas les droits sur cet article');

				if ($user && !$this->isVisible($article, $user->id))
					abort(403, 'Vous n\'avez pas les droits sur cet article');

				if ($verb !== 'get' && \Scopes::isUserToken($request) && !$article->owned_by->isArticleManageableBy(\Auth::id()))
					abort(403, 'Vous n\'avez pas les droits suffisants');
	        }
	        else {
				if (!$this->isVisible($article)) {
					abort(403, 'Vous n\'avez pas les droits sur cet article');
				}
	        }

			return $article;
		}

		abort(404, 'Impossible de trouver l\'article');
	}
}
