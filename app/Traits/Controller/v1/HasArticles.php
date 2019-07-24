<?php
/**
 * Add the controller an access to Articles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\Event;
use App\Models\Asso;
use App\Models\User;
use App\Models\Article;
use App\Models\Client;
use App\Models\Model;
use Illuminate\Http\Request;

trait HasArticles
{
    use HasEvents {
        HasEvents::tokenCanSee as protected tokenCanSeeEvent;
    }

    /**
     * Return if the token can see the resource.
     *
     * @param  Request $request
     * @param  Model   $model
     * @param  string  $verb
     * @param  string  $type
     * @return boolean
     */
    protected function tokenCanSee(Request $request, Model $model, string $verb='get', string $type='articles')
    {
        if ($model instanceof Article) {
            $scopeHead = \Scopes::getTokenType($request);
            $modelType = \ModelResolver::getName($model->owned_by_type);

            if (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$modelType.'s-owned')) {
                return true;
            }

            if (((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$modelType.'s-owned-user'))
                && \Auth::id()
                && $model->created_by_type === User::class
                && $model->created_by_id === \Auth::id())
                || ((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$modelType.'s-owned-client'))
                && $model->created_by_type === Client::class
                && $model->created_by_id === \Scopes::getClient($request)->id)
                || ((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$modelType.'s-owned-asso'))
                && $model->created_by_type === Asso::class
                && $model->created_by_id === \Scopes::getClient($request)->asso->id)) {
                return true;
            }

            return \Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$modelType.'s-created');
        }

        return $this->tokenCanSeeEvent($request, $model, $verb);
    }

    /**
     * Retrieve the requested article.
     *
     * @param  Request $request
     * @param  User    $user
     * @param  string  $article_id
     * @param  string  $verb
     * @return Article
     */
    protected function getArticle(Request $request, User $user=null, string $article_id, string $verb='get')
    {
        Article::setUserForVisibility($user);
        $article = Article::findSelection($article_id);

        if ($article) {
            // Public access check.
            if (\Scopes::isOauthRequest($request)) {
                if (!$this->tokenCanSee($request, $article, $verb, 'articles')) {
                    abort(403, 'L\'application n\'a pas les droits sur cet article');
                }

                if ($verb !== 'get' && \Scopes::isUserToken($request)
                    && !$article->owned_by->isArticleManageableBy($user->id)) {
                    abort(403, 'Vous n\'avez pas les droits suffisants');
                }
            }

            return $article;
        }

        abort(404, 'Impossible de trouver l\'article');
    }
}
