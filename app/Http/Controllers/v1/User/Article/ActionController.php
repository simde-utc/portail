<?php
/**
 * Manages user's actions on articles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\User\Article;

use App\Http\Controllers\v1\Controller;
use App\Models\User;
use App\Models\Asso;
use App\Models\Visibility;
use App\Models\ArticleAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserArticleActionRequest;
use App\Models\Article;
use App\Traits\Controller\v1\{
	HasUserBulkMethods, HasArticles
};
use App\Exceptions\PortailException;

class ActionController extends Controller
{
    use HasUserBulkMethods, HasArticles;

    /**
     * Must be able to see articles and handle users actions.
     */
    public function __construct()
    {
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren(['user-get-articles-assos', 'user-get-articles-groups'],
                    ['client-get-articles-assos', 'client-get-articles-groups']),
                \Scopes::matchOne('user-get-articles-actions-user', 'client-get-articles-actions-user')
            ),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren(['user-get-articles-assos', 'user-get-articles-groups'],
                    ['client-get-articles-assos', 'client-get-articles-groups']),
                \Scopes::matchOne('user-create-articles-actions-user', 'client-create-articles-actions-user')
            ),
            ['only' => ['store']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren(['user-get-articles-assos', 'user-get-articles-groups'],
                    ['client-get-articles-assos', 'client-get-articles-groups']),
                \Scopes::matchOne('user-edit-articles-actions-user', 'client-edit-articles-actions-user')
            ),
            ['only' => ['update']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren(['user-get-articles-assos', 'user-get-articles-groups'],
                    ['client-get-articles-assos', 'client-get-articles-groups']),
                \Scopes::matchOne('user-manage-articles-actions-user', 'client-manage-articles-actions-user')
            ),
            ['only' => ['remove']]
        );
        // Can index, show and create, edit and remove actions for multiple users in a raw.
        $this->middleware(
            \Scopes::matchAnyClient(),
            ['only' => ['bulkIndex', 'bulkStore', 'bulkShow', 'bulkUpdate', 'bulkDestroy']]
        );
    }

    /**
     * Lists user's actions.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $article_id
     * @return JsonResponse
     */
    public function index(Request $request, string $user_id, string $article_id=null): JsonResponse
    {
        if (is_null($article_id)) {
            list($user_id, $article_id) = [$article_id, $user_id];
        }

        $user = $this->getUser($request, $user_id);
        $article = $this->getArticle($request, $user, $article_id);
        $actions = $article->actions()->where('user_id', $user->id)->allToArray();

        return response()->json($actions);
    }

    /**
     * Creates a user actions.
     *
     * @param UserArticleActionRequest $request
     * @param string                   $user_id
     * @param string                   $article_id
     * @return JsonResponse
     */
    public function store(UserArticleActionRequest $request, string $user_id, string $article_id=null): JsonResponse
    {
        if (is_null($article_id)) {
            list($user_id, $article_id) = [$article_id, $user_id];
        }

        $user = $this->getUser($request, $user_id);
        $article = $this->getArticle($request, $user, $article_id);

        $action = ArticleAction::create(array_merge(
            $request->input(),
            [
                'article_id' => $article_id,
                'user_id' => $user->id,
            ]
        ));

        return response()->json($action, 201);
    }

    /**
     * Shows a user actions.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $article_id
     * @param string  $key
     * @return JsonResponse
     */
    public function show(Request $request, string $user_id, string $article_id, string $key=null): JsonResponse
    {
        if (is_null($key)) {
            list($user_id, $article_id, $key) = [$key, $user_id, $article_id];
        }

        $user = $this->getUser($request, $user_id);
        $article = $this->getArticle($request, $user, $article_id);
        $action = $article->actions()->where('user_id', $user->id)->key($key);

        return response()->json($action, 200);
    }

    /**
     * Updates a user actions.
     *
     * @param UserArticleActionRequest $request
     * @param string                   $user_id
     * @param string                   $article_id
     * @param string                   $key
     * @return JsonResponse
     */
    public function update(UserArticleActionRequest $request, string $user_id, string $article_id,
        string $key=null): JsonResponse
    {
        if (is_null($key)) {
            list($user_id, $article_id, $key) = [$key, $user_id, $article_id];
        }

        $user = $this->getUser($request, $user_id);
        $article = $this->getArticle($request, $user, $article_id);

        try {
            $action = $article->actions()->where('user_id', $user->id)->key($key);
            $action->value = $request->input('value', $action->value);

            if ($action->update()) {
                return response()->json($action);
            } else {
                abort(503, 'Erreur lors de la modification');
            }
        } catch (PortailException $e) {
            abort(404, 'Cette personne ne possède pas cette action, ou il ne peut être modifié');
        }
    }

    /**
     * Deletes a user actions.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $article_id
     * @param string  $key
     * @return void
     */
    public function destroy(Request $request, string $user_id, string $article_id, string $key=null)
    {
        if (is_null($key)) {
            list($user_id, $article_id, $key) = [$key, $user_id, $article_id];
        }

        $user = $this->getUser($request, $user_id);
        $article = $this->getArticle($request, $user, $article_id);

        try {
            $action = $article->actions()->where('user_id', $user->id)->key($key);

            if ($action->delete()) {
                abort(204);
            } else {
                abort(503, 'Erreur lors de la suppression');
            }
        } catch (PortailException $e) {
            abort(404, 'Cette personne ne possède pas cette action, ou il ne peut être modifié');
        }
    }
}
