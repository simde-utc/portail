<?php

namespace App\Http\Controllers\v1\Article;

use App\Http\Controllers\v1\Controller;
use App\Models\User;
use App\Models\Asso;
use App\Models\Visibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Traits\HasVisibility;
use App\Interfaces\Model\CanHaveArticles;
use App\Traits\Controller\v1\HasArticles;

/**
 * @resource Article
 *
 * Les articles écrits et postés par les associations
 */
class ActionController extends Controller
{
	use HasArticles;

	/**
	 * Scopes Article
	 *
	 * Les Scopes requis pour manipuler les Articles
	 */
 	public function __construct() {
 		$this->middleware(
 			\Scopes::matchOneOfDeepestChildren('user-get-articles', 'client-get-articles'),
 			['only' => ['index', 'show']]
 		);
 		$this->middleware(
 			\Scopes::matchOneOfDeepestChildren('user-create-articles', 'client-create-articles'),
 			['only' => ['store']]
 		);
 		$this->middleware(
 			\Scopes::matchOneOfDeepestChildren('user-edit-articles', 'client-edit-articles'),
 			['only' => ['update']]
 		);
 		$this->middleware(
 			\Scopes::matchOneOfDeepestChildren('user-manage-articles', 'client-manage-articles'),
 			['only' => ['destroy']]
 		);
 	}

	/**
	 * List Articles
	 *
	 * Retourne la liste des articles. ?all pour voir ceux en plus des assos suivies, ?notRemoved pour uniquement cacher les articles non visibles
	 * @param \Illuminate\Http\Request $request
	 * @return JsonResponse
	 */
	public function index(Request $request, int $article_id): JsonResponse {
		$article = $this->getArticle($request, \Auth::user(), $article_id);
		$actions = $article->actions()->groupToArray();

		return response()->json($actions, 200);
	}

	/**
	 * Create Article
	 *
	 * Créer un article
	 * @param ArticleRequest $request
	 * @return JsonResponse
	 */
	public function store(Request $request): JsonResponse {
		abort(419);
	}

	/**
	 * Show Article
	 *
	 * Affiche l'article s'il existe et si l'utilisateur peut le voir.
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, int $article_id, string $key): JsonResponse {
		$article = $this->getArticle($request, \Auth::user(), $article_id);
		$actions = $article->actions()->where('key', $key)->groupToArray();

		return response()->json($actions, 200);
	}

	/**
	 * Update Article
	 *
	 * Met à jour l'article s'il existe
	 * @param ArticleRequest $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, int $id): JsonResponse {
		abort(419);
	}

	/**
	 * Delete Article
	 *
	 * Supprime l'article s'il existe
	 * @param ArticleRequest $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(ArticleRequest $request, $id): JsonResponse {
		abort(419);
	}
}
