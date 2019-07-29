<?php
/**
 * Manage les actions des articles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Article;

use App\Http\Controllers\v1\Controller;
use App\Models\User;
use App\Models\Asso;
use App\Models\Visibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Traits\Controller\v1\HasArticles;
use App\Exception\PortailException;

class ActionController extends Controller
{
    use HasArticles;

    /**
     * Must be able to see articles and manage articles actions.
     */
    public function __construct()
    {
        $this->middleware(
            array_merge(
		        \Scopes::matchOneOfDeepestChildren(['user-get-articles-assos', 'user-get-articles-groups'],
                    ['client-get-articles-assos', 'client-get-articles-groups']),
            	\Scopes::matchOne('user-get-articles-actions', 'client-get-articles-actions')
	        ),
	        ['only' => ['all', 'get']]
        );
        $this->middleware(
            array_merge(
		        \Scopes::matchOneOfDeepestChildren(['user-get-articles-assos', 'user-get-articles-groups'],
                    ['client-get-articles-assos', 'client-get-articles-groups']),
            	\Scopes::matchOne('user-create-articles-actions', 'client-create-articles-actions')
	        ),
	        ['only' => ['create']]
        );
        $this->middleware(
            array_merge(
		        \Scopes::matchOneOfDeepestChildren(['user-get-articles-assos', 'user-get-articles-groups'],
                    ['client-get-articles-assos', 'client-get-articles-groups']),
            	\Scopes::matchOne('user-edit-articles-actions', 'client-edit-articles-actions')
	        ),
	        ['only' => ['edit']]
        );
        $this->middleware(
            array_merge(
		        \Scopes::matchOneOfDeepestChildren(['user-get-articles-assos', 'user-get-articles-groups'],
                    ['client-get-articles-assos', 'client-get-articles-groups']),
            	\Scopes::matchOne('user-manage-articles-actions', 'client-manage-articles-actions')
	        ),
	        ['only' => ['remove']]
        );
    }

    /**
     * List the article's actions.
     *
     * @param Request $request
     * @param string  $article_id
     * @return JsonResponse
     */
    public function index(Request $request, string $article_id): JsonResponse
    {
        $article = $this->getArticle($request, \Auth::user(), $article_id);
        $actions = $article->actions()->groupToArray();

        return response()->json($actions, 200);
    }

    /**
     * Impossible to add an article action from this controller.
     *
     * @param Request $request
     * @param string  $article_id
     * @return void
     */
    public function store(Request $request, string $article_id): void
    {
        abort(419);
    }

    /**
     * Show an article action.
     *
     * @param Request $request
     * @param string  $article_id
     * @param string  $key
     * @return JsonResponse
     */
    public function show(Request $request, string $article_id, string $key): JsonResponse
    {
        $article = $this->getArticle($request, \Auth::user(), $article_id);
        $actions = $article->actions()->where('key', $key)->groupToArray();

        return response()->json($actions, 200);
    }

    /**
     * Impossible to update an article action.
     *
     * @param Request $request
     * @param string  $article_id
     * @param string  $key
     * @return void
     */
    public function update(Request $request, string $article_id, string $key): void
    {
        abort(419);
    }

    /**
     * Impossible to delete an article action.
     *
     * @param Request $request
     * @param string  $article_id
     * @param string  $key
     * @return void
     */
    public function destroy(Request $request, string $article_id, string $key): void
    {
        abort(419);
    }
}
