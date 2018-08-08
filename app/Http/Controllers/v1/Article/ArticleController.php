<?php

namespace App\Http\Controllers\v1\Article;

use App\Http\Controllers\v1\Controller;
use App\Facades\Scopes;
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
class ArticleController extends Controller
{
	use HasArticles;

	/**
	 * Scopes Article
	 *
	 * Les Scopes requis pour manipuler les Articles
	 */
 	public function __construct() {
 		$this->middleware(
 			\Scopes::matchOneOfDeepestChildren(['user-get-articles-assos', 'user-get-articles-groups'], ['client-get-articles-assos', 'client-get-articles-groups']),
 			['only' => ['index', 'show']]
 		);
 		$this->middleware(
			\Scopes::matchOneOfDeepestChildren(['user-create-articles-assos', 'user-create-articles-groups'], ['client-create-articles-assos', 'client-create-articles-groups']),
 			['only' => ['store']]
 		);
 		$this->middleware(
			\Scopes::matchOneOfDeepestChildren(['user-edit-articles-assos', 'user-edit-articles-groups'], ['client-edit-articles-assos', 'client-edit-articles-groups']),
 			['only' => ['update']]
 		);
 		$this->middleware(
			\Scopes::matchOneOfDeepestChildren(['user-manage-articles-assos', 'user-manage-articles-groups'], ['client-manage-articles-assos', 'client-manage-articles-groups']),
 			['only' => ['destroy']]
 		);
 	}

	public function getCreaterOrOwner(Request $request, string $verb = 'create', string $type = 'created') {
		$scopeHead = \Scopes::isUserToken($request) ? 'user' : 'client';
		$scope = $scopeHead.'-'.$verb.'-articles-'.$request->input($type.'_by_type',\Scopes::isClientToken($request) ? 'client' : 'user').'s-'.$type;

		if ($type === 'owned')
			$scope = array_keys(\Scopes::getRelatives($scopeHead.'-'.$verb.'-articles-'.$request->input($type.'_by_type').'s-'.$type));

		if (!\Scopes::hasOne($request, $scope))
			abort(403, 'Il ne vous est pas autorisé de créer d\'articles');

		if ($request->filled($type.'_by_type')) {
			if ($request->filled($type.'_by_id')) {
				$createrOrOwner = \ModelResolver::getModel($request->input($type.'_by_type'))->find($request->input($type.'_by_id'));

				if (\Auth::id() && !$createrOrOwner->isArticleManageableBy(\Auth::id()))
					abort(403, 'L\'utilisateur n\'a pas les droits de création');
			}
			else if ($request->input($type.'_by_type', 'client') === 'client')
				$createrOrOwner = \Scopes::getClient($request);
			else if ($request->input($type.'_by_type', 'client') === 'asso')
				$createrOrOwner = \Scopes::getClient($request)->asso;
		}

		if (!isset($createrOrOwner))
			$createrOrOwner = \Scopes::isClientToken($request) ? \Scopes::getClient($request) : \Auth::user();

		if (!($createrOrOwner instanceof CanHaveArticles))
			abort(400, 'La personne créatrice/possédeur doit au moins pouvoir avoir un article');

		return $createrOrOwner;
	}

	/**
	 * List Articles
	 *
	 * Retourne la liste des articles. ?all pour voir ceux en plus des assos suivies, ?notRemoved pour uniquement cacher les articles non visibles
	 * @param \Illuminate\Http\Request $request
	 * @return JsonResponse
	 */
	public function index(Request $request): JsonResponse {
		$articles = Article::getSelection()->filter(function ($article) use ($request) {
			return $this->tokenCanSee($request, $article, 'get') && (!\Auth::id() || $this->isVisible($article, \Auth::id()));
		})->values()->map(function ($article) {
			return $article->hideData();
		});

		return response()->json($articles, 200);
	}

	/**
	 * Create Article
	 *
	 * Créer un article
	 * @param ArticleRequest $request
	 * @return JsonResponse
	 */
	public function store(Request $request): JsonResponse {
		$inputs = $request->all();

		$owner = $this->getCreaterOrOwner($request, 'create', 'owned');

		if ($request->input('created_by_type') === 'client'
			&& $request->input('created_by_id', \Scopes::getClient($request)->id) === \Scopes::getClient($request)->id
			&& \Scopes::hasOne($request, (\Scopes::isClientToken($request) ? 'client' : 'user').'-create-articles-'.\ModelResolver::getName($owner).'s-owned-client'))
				$creater = \Scopes::getClient($request);
		else if ($request->input('created_by_type') === 'asso'
			&& $request->input('created_by_id', \Scopes::getClient($request)->asso->id) === \Scopes::getClient($request)->asso->id
			&& \Scopes::hasOne($request, (\Scopes::isClientToken($request) ? 'client' : 'user').'-create-articles-'.\ModelResolver::getName($owner).'s-owned-asso'))
			$creater = \Scopes::getClient($request)->asso;
		else
			$creater = $this->getCreaterOrOwner($request, 'create', 'created');

		$inputs['created_by_id'] = $creater->id;
		$inputs['created_by_type'] = get_class($creater);
		$inputs['owned_by_id'] = $owner->id;
		$inputs['owned_by_type'] = get_class($owner);

		if ($request->filled('event_id')) // On fait vérifier que la personne à les droits sur l'event
			$this->getEvent($request, \Auth::user(), $inputs['event_id']);

		$article = Article::create($inputs);

		if ($article) {
			$article = $this->getArticle($request, \Auth::user(), $article->id);

			return response()->json($article->hideSubData(), 201);
		}
		else
			return response()->json(['message' => 'Impossible de créer le article'], 500);
	}

	/**
	 * Show Article
	 *
	 * Affiche l'article s'il existe et si l'utilisateur peut le voir.
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, int $id): JsonResponse {
		$article = $this->getArticle($request, \Auth::user(), $id);

		return response()->json($article->hideSubData(), 200);
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
		$article = $this->getCalendar($request, \Auth::user(), $id, 'edit');
		$inputs = $request->all();

		if ($request->filled('owned_by_type')) {
			$owner = $this->getCreaterOrOwner($request, 'edit', 'owned');

			$inputs['owned_by_id'] = $owner->id;
			$inputs['owned_by_type'] = get_class($owner);
		}

		if ($request->filled('event_id')) // On fait vérifier que la personne à les droits sur l'event
			$this->getEvent($request, \Auth::user(), $inputs['event_id']);

		if ($article->update($inputs))
			return response()->json($article->hideData(), 200);
		else
			abort(500, 'Impossible de modifier l\'article');
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
		$article = $this->getArticle($request, \Auth::user(), $id, 'remove');

		if ($article->delete())
			abort(204);
		else
			abort(500, 'L\'article n\'a pas pu être supprimé');
	}
}
