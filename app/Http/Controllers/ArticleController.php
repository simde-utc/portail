<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Services\Visible\ArticleVisible;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    //TODO Argument permettant de le passer en hide
    public function index(Request $request)
    {
        $articles = Article::all();
        if(isset($request['all'])) //Si est dans la route est mis un argument all
            return response()->json(ArticleVisible::hide($articles), 200); //On renvoie tous les articles en cachant les non visibles
	    //Sinon
	    return response()->json(ArticleVisible::with($articles),200); //On ne renvoie que ceux qui sont visibles
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
        	return response()->json(ArticleVisible::hide($article),200); //On renvoie l'article demandé, mais en le cachant si l'user n'a pas les droits nécessaires
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
		if($article) {
			$ok = $article->update($request->input());
			if ($ok)
				return response()->json($article, 201);
			return response()->json(['message' => 'impossible de modifier l\'article'], 500);
		}
		return response()->json(['message'=>'L\'article demandé n\'a pas été trouvé'],404);
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
    	if($article){
			$ok = $article->delete();
			if($ok)
				return response()->json(['message'=>'L\'article a bien été supprimé'],200);
			return response()->json(['message'=>'Une erreur est survenue'],500);
	    }
	    return response()->json(['message'=>'L\'article demandé n\'a pas été trouvé'],404);
    }
}
