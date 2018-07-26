<?php

namespace App\Http\Controllers\v1\Article;

use App\Http\Controllers\v1\Controller;
use App\Facades\Scopes;
use App\Models\Asso;
use App\Models\Visibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Traits\HasVisibility;

/**
 * @resource Article
 *
 * Les articles écrits et postés par les associations
 */
class ArticleController extends Controller
{
	use HasVisibility;

	// TODO abort

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
			\Scopes::matchOneOfDeepestChildren('user-set-articles', 'client-set-articles'),
			['only' => ['store', 'update']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-manage-articles', 'client-manage-articles'),
			['only' => ['destroy']]
		);
	}

	public function isPrivate($user_id, $model = null) {
		if ($model === null)
			return false;

		if ($model->user_id && $model->user_id == $user_id)
			return true;

		try {
			return $model->currentAllMembers()->wherePivot('user_id', $user_id)->count() > 0;
		}
		catch (Exception $e) {}

        return false;
    }

	/**
	 * List Articles
	 *
	 * Retourne la liste des articles. ?all pour voir ceux en plus des assos suivies, ?notRemoved pour uniquement cacher les articles non visibles
	 * @param \Illuminate\Http\Request $request
	 * @return JsonResponse
	 */
	public function index(Request $request): JsonResponse {
		if ($request->user()) {
			if (isset($request['all'])) {
				$articles = Article::get();
			}
			else {
				$articles = Article::whereHas('collaborators', function ($query) use ($request) {
					$query->whereIn('asso_id', array_merge(
						$request->user()->currentAssos()->pluck('assos.id')->toArray()
					));
				})->get();
			}
		}
		else {
			$articles = Scopes::has($request, 'client-get-articles') && isset($request['all']) ? Article::get() : Article::where('visibility_id', Visibility::where('type', 'public')->first()->id)->get();
		}
		return response()->json($request->user() ? $this->hide($articles, !isset($request['notRemoved'])) : $articles, 200);
	}

	/**
	 * Create Article
	 *
	 * Créer un article. ?add_collaborators[ids] pour ajouter des collaborateurs lors de la création
	 * @param ArticleRequest $request
	 * @return JsonResponse
	 */
	public function store(ArticleRequest $request): JsonResponse {
		$article = Article::create($request->input());

		if (isset($request['add_collaborators'])) {
			$collaborators = is_array($request['add_collaborators']) ? $request['add_collaborators'] : [$request['add_collaborators']];
			foreach ($collaborators as $key => $asso_id) {
				if (Asso::find($asso_id))
					$article->collaborators()->attach($asso_id);
			}
		}

		if ($article)
			return response()->json(Article::find($article->id), 201);
		else
			return response()->json(['message' => 'Impossible de créer l\'article'], 500);
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
		$article = Article::find($id);
		if (!isset($article))
			return response()->json(['message' => 'Impossible de trouver l\'article demandé'], 404);
		else
			return response()->json(Scopes::has($request, 'client-get-articles') ? $article : $this->hide($article, false), 200);
	}

	/**
	 * Update Article
	 *
	 * Met à jour l'article s'il existe ?add_collaborators[ids] pour ajouter des collaborateurs ?remove_collaborators[ids] pour en enlever (sauf l'asso créatrice)
	 * @param ArticleRequest $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(ArticleRequest $request, int $id): JsonResponse {
		$article = Article::find($id);
		if (!isset($article))
			return response()->json(['message' => 'Impossible de trouver l\'article demandé'], 404);

		if ($article->update($request->input())) {

			if (isset($request['add_collaborators'])) {
				$collaborators = is_array($request['add_collaborators']) ? $request['add_collaborators'] : [$request['add_collaborators']];
				foreach ($collaborators as $key => $asso_id) {
					if (Asso::find($asso_id))
						$article->collaborators()->attach($asso_id);
				}
			}
			if (isset($request['remove_collaborators'])) {
				$collaborators = is_array($request['remove_collaborators']) ? $request['remove_collaborators'] : [$request['remove_collaborators']];
				foreach ($collaborators as $key => $asso_id) {
					if (Asso::find($asso_id) && $asso_id != $article->asso_id)
						$article->collaborators()->detach($asso_id);
				}
			}
			return response()->json(Article::find($id), 201);
		}
		else
			return response()->json(['message' => 'Impossible de modifier l\'article'], 500);
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
		$article = Article::find($id);
		if (!isset($article))
			return response()->json(['message' => 'Impossible de trouver l\'article demandé'], 404);

		if ($article->delete())
			return response()->json(['message' => 'L\'article a bien été supprimé'], 200);
		else
			return response()->json(['message' => 'L\'article n\'a pas pu être supprimé'], 500);
	}
}
