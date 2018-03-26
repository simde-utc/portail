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
     *
     * @return \Illuminate\Http\Response
     */
    //TODO Argument permettant de le passer en hide
    public function index()
    {
        $articles = Article::all();
        return response()->json(ArticleVisible::with($articles), 200); //On ne renvoie que les articles visibles
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
        	return response()->json($article, 200);
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
        	return response()->json(ArticleVisible::hide($article),200); //On renvoie l'article demandé, mais en le cachant si l'user n'a pas les droits nécessaires
        else
        	return response()->json(['message' => 'Article not found'], 404);
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
		$modified = $article->update($request->input());
		if ($modified)
			return response()->json($article, 200);
		else
			return response()->json(['message'=>'impossible de modifier l\'article'],500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    	//TODO : delete foreign references before. Cascade ?
    }
}
