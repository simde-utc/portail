<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Visibility;
use App\Services\Visible\Visible;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Services\Visible\ArticleVisible;

class ArticleController extends Controller
{
    public function __construct() {
		$this->middleware(\Scopes::matchOne(['client-get-articles-public', 'user-get-articles-followed-now', 'user-get-articles-done-now']), ['only' => ['index', 'show']]);
        $this->middleware(\Scopes::matchOne(['client-set-articles', 'user-set-articles-followed-now', 'user-set-articles-done-now']), ['only' => ['store', 'update', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    //TODO Argument permettant de le passer en hide
    public function index(Request $request)
    {
		if ($request->user()) {
			if (isset($request['all'])){
				$articles = Article::where('visibility_id', '<=', Visibility::where('type', Visible::getType($request->user()->id ))->first()->id)->get();
			}
			else {
				/*$articles = Article::with('collaborators:id')->whereIn('asso_id', array_merge(
					$request->user()->currentAssos()->pluck('assos.id')->toArray()
					//$request->user()->currentAssosFollowed()->pluck('assos.id')->toArray(),
				))->get();*/
				$articles = Article::with('collaborators')->whereHas('collaborators', function($q) use ($request){
					$q->whereIn('collaborators.asso_id', array_merge(
						$request->user()->currentAssos()->pluck('assos.id')->toArray()
					))->get();
				});
				return response()->json($articles);
			}
		}
		else
		 	$articles = \Scopes::has('client-get-article') && isset($request['all']) ? Article::all() : Article::where('visibility', function ($query) { $query->where('type', 'public'); })->get();

    	return response()->json($request->user() ? ArticleVisible::with($articles, $request->user()->id) : $articles, 200); //On ne renvoie que ceux qui sont visibles
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
     * Display the specified resource.
     *
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ArticleRequest $request, $id){
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    	$article = Article::find($id);

    	if($article) {

			if ($article->delete())
				return response()->json(['message' => 'L\'article a bien été supprimé'], 200);
			else
				return response()->json(['message' => 'Une erreur est survenue'],500);
	    }

		return response()->json(['message' => 'L\'article demandé n\'a pas été trouvé'], 404);
    }
}
