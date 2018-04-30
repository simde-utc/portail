<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Models\Visibility;
use App\Services\Visible\Visible;
use App\Services\Visible\ArticleVisible;

/**
 * @resource Article
 *
 * Les articles écrits et postés par les associations
 */
class ArticleController extends Controller
{
	/**
	 * Scopes Article
	 *
	 * Les Scopes requis pour manipuler les Articles
	 */
	public function __construct() {
		$this->middleware(
			\Scopes::matchOne(
				['user-get-articles-followed-now', 'user-get-articles-done-now'],
				['client-get-articles-public']
			),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['user-manage-articles']
			),
			['only' => ['store', 'update', 'destroy']]
		);
	}

	/**
	 * List Articles
	 *
	 * Retourne la liste des articles
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		// TODO Argument permettant de le passer en hide
		if ($request->user()) {
			if (isset($request['all'])){
				$articles = Article::where('visibility_id', '<=', Visibility::where('type', Visible::getType($request->user()->id ))->first()->id)->get();
			}
			else {
				$articles = Article::whereHas('collaborators', function ($query) use ($request){
					$query->whereIn('asso_id', array_merge(
						$request->user()->currentAssos()->pluck('assos.id')->toArray()
					));
				})->get();
			}
		}
		else
			$articles = \Scopes::has('client-get-article') && isset($request['all']) ? Article::all() : Article::where('visibility', function ($query) { $query->where('type', 'public'); })->get();

		// On ne renvoie que ceux qui sont visibles
		return response()->json($request->user() ? ArticleVisible::with($articles, $request->user()->id) : $articles, 200);
	}

	/**
	 * Create Article
	 *
	 * Créer un article
	 * @param  \Illuminate\Http\ArticleRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(ArticleRequest $request)
	{
		$article = Article::create($request->input());

		if ($article)
			return response()->json($article, 201);
		else
			return response()->json(['message' => 'impossible de créer l\'article'], 500);
	}

	/**
	 * Show Article
	 *
	 * Affiche l'article s'il existe et si l'utilisateur peut le voir
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$article = Article::find($id);

		if ($article)
			return response()->json(ArticleVisible::hide($article, $request->user()->id), 200); //On renvoie l'article demandé, mais en le cachant si l'user n'a pas les droits nécessaires
		else
			return response()->json(['message' => 'L\'article demandé n\'a pas été trouvé'], 404);
	}

	/**
	 * Update Article
	 *
	 * Met à jour l'article s'il existe
	 * @param  \Illuminate\Http\ArticleRequest  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(ArticleRequest $request, $id)
	{
		$article = Article::find($id);

		if ($article) {
			if ($article->update($request->input()))
				return response()->json($article, 201);
			else
				return response()->json(['message' => 'impossible de modifier l\'article'], 500);
		}

		return response()->json(['message' => 'L\'article demandé n\'a pas été trouvé'], 404);
	}

	/**
	 * Delete Article
	 *
	 * Supprime l'article s'il existe
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$article = Article::find($id);

		if ($article) {
			if ($article->delete())
				return response()->json(['message' => 'L\'article a bien été supprimé'], 200);
			else
				return response()->json(['message' => 'Une erreur est survenue'],500);
		}

		return response()->json(['message' => 'L\'article demandé n\'a pas été trouvé'], 404);
	}
}
