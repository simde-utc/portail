<?php

namespace App\Http\Controllers\v1\User\Article;

use App\Http\Controllers\v1\Controller;
use App\Models\User;
use App\Models\Asso;
use App\Models\Visibility;
use App\Models\ArticleAction;
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
   		$this->middleware(array_merge(
 				\Scopes::matchOneOfDeepestChildren(['user-get-articles-assos', 'user-get-articles-groups'], ['client-get-articles-assos', 'client-get-articles-groups']),
   				\Scopes::matchOne('user-get-articles-actions-user', 'client-get-articles-actions-user')
 			),
   			['only' => ['index', 'show']]
   		);
   		$this->middleware(array_merge(
 				\Scopes::matchOneOfDeepestChildren(['user-get-articles-assos', 'user-get-articles-groups'], ['client-get-articles-assos', 'client-get-articles-groups']),
   				\Scopes::matchOne('user-create-articles-actions-user', 'client-create-articles-actions-user')
 			),
   			['only' => ['store']]
   		);
   		$this->middleware(array_merge(
 				\Scopes::matchOneOfDeepestChildren(['user-get-articles-assos', 'user-get-articles-groups'], ['client-get-articles-assos', 'client-get-articles-groups']),
   				\Scopes::matchOne('user-edit-articles-actions-user', 'client-edit-articles-actions-user')
 			),
   			['only' => ['update']]
   		);
   		$this->middleware(array_merge(
 				\Scopes::matchOneOfDeepestChildren(['user-get-articles-assos', 'user-get-articles-groups'], ['client-get-articles-assos', 'client-get-articles-groups']),
   				\Scopes::matchOne('user-manage-articles-actions-user', 'client-manage-articles-actions-user')
 			),
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
	public function index(Request $request, int $user_id, int $article_id = null): JsonResponse {
		if (is_null($article_id))
			list($user_id, $article_id) = [$article_id, $user_id];

		$user = $this->getUser($request, $user_id);
		$article = $this->getArticle($request, $user, $article_id);
		$actions = $article->actions()->where('user_id', $user->id)->allToArray();

		return response()->json($actions, 200);
	}

	/**
	 * Create Article
	 *
	 * Créer un article
	 * @param ArticleRequest $request
	 * @return JsonResponse
	 */
	public function store(Request $request, int $user_id, int $article_id = null): JsonResponse {
		if (is_null($article_id))
			list($user_id, $article_id) = [$article_id, $user_id];

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
	 * Show Article
	 *
	 * Affiche l'article s'il existe et si l'utilisateur peut le voir.
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, int $user_id, $article_id, string $key = null): JsonResponse {
		if (is_null($key))
			list($user_id, $article_id, $key) = [$key, $user_id, $article_id];

		$user = $this->getUser($request, $user_id);
		$article = $this->getArticle($request, $user, $article_id);
		$action = $article->actions()->where('user_id', $user->id)->key($key);

		return response()->json($action, 200);
	}

	/**
	 * Update Article
	 *
	 * Met à jour l'article s'il existe
	 * @param ArticleRequest $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, int $user_id, $article_id, string $key = null): JsonResponse {
		if (is_null($key))
			list($user_id, $article_id, $key) = [$key, $user_id, $article_id];

		$user = $this->getUser($request, $user_id);
		$article = $this->getArticle($request, $user, $article_id);

		try {
			$action = $article->actions()->where('user_id', $user->id)->key($key);
			$action->value = $request->input('value', $action->value);

			if ($action->update())
				return response()->json($action);
			else
				abort(503, 'Erreur lors de la modification');
		}
		catch (PortailException $e) {
			abort(404, 'Cette personne ne possède pas cette action, ou il ne peut être modifié');
		}
	}

	/**
	 * Delete Article
	 *
	 * Supprime l'article s'il existe
	 * @param ArticleRequest $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(ArticleRequest $request, int $user_id, $article_id, string $key = null): JsonResponse {
		if (is_null($key))
			list($user_id, $article_id, $key) = [$key, $user_id, $article_id];

		$user = $this->getUser($request, $user_id);
		$article = $this->getArticle($request, $user, $article_id);

		try {
			$action = $article->actions()->where('user_id', $user->id)->key($key);

			if ($action->delete())
				abort(204);
			else
				abort(503, 'Erreur lors de la suppression');
		}
		catch (PortailException $e) {
			abort(404, 'Cette personne ne possède pas cette action, ou il ne peut être modifié');
		}
	}
}
