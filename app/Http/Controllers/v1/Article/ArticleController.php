<?php
/**
 * Gère les articles.
 *
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Article;

use App\Http\Controllers\v1\Controller;
use App\Models\Model;
use App\Models\User;
use App\Models\Asso;
use App\Models\Visibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Models\Tag;
use App\Traits\HasVisibility;
use App\Interfaces\Model\CanHaveArticles;
use App\Traits\Controller\v1\HasArticles;
use App\Traits\Controller\v1\HasImages;

class ArticleController extends Controller
{
    use HasArticles, HasImages;

    /**
     * Nécessité de gérer les articles.
     * Lecture publique.
     */
    public function __construct()
    {
        $this->middleware(
	        \Scopes::allowPublic()->matchOneOfDeepestChildren(
                ['user-get-articles-assos', 'user-get-articles-groups'],
                ['client-get-articles-assos', 'client-get-articles-groups']
            ),
	        ['only' => ['index', 'show']]
        );
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren(
                ['user-create-articles-assos', 'user-create-articles-groups'],
                ['client-create-articles-assos', 'client-create-articles-groups']
            ),
	        ['only' => ['store']]
        );
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren(
                ['user-edit-articles-assos', 'user-edit-articles-groups'],
                ['client-edit-articles-assos', 'client-edit-articles-groups']
            ),
	        ['only' => ['update']]
        );
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren(
                ['user-manage-articles-assos', 'user-manage-articles-groups'],
                ['client-manage-articles-assos', 'client-manage-articles-groups']
            ),
	        ['only' => ['destroy']]
        );
    }

    /**
     * Récupère le créateur ou le owner.
     *
     * @param  Request $request
     * @param  string  $verb
     * @param  string  $type
     * @return Model
     */
    public function getCreatorOrOwner(Request $request, string $verb='create', string $type='created')
    {
        $scopeHead = \Scopes::isUserToken($request) ? 'user' : 'client';
        $scope = $scopeHead.'-'.$verb.'-articles-'.$request->input($type.'_by_type', $scopeHead).'s-'.$type;

        if ($type === 'owned') {
            $scope = array_keys(\Scopes::getRelatives(
                $scopeHead.'-'.$verb.'-articles-'.$request->input($type.'_by_type').'s-'.$type
            ));

            if (count($scope) === 0) {
                abort(403, 'Non autorisé a possédé un article');
            }
        }

        if (!\Scopes::hasOne($request, $scope)) {
            abort(403, 'Il ne vous est pas autorisé de créer d\'articles');
        }

        if ($request->filled($type.'_by_type')) {
            if ($request->filled($type.'_by_id')) {
                $creatorOrOwner = \ModelResolver::findModel(
                    $request->input($type.'_by_type'),
                    $request->input($type.'_by_id')
                );

                if (\Auth::id() && !$creatorOrOwner->isArticleManageableBy(\Auth::id())) {
                    abort(403, 'L\'utilisateur n\'a pas les droits de création');
                }
            } else if ($request->input($type.'_by_type', 'client') === 'client') {
                $creatorOrOwner = \Scopes::getClient($request);
            } else if ($request->input($type.'_by_type', 'client') === 'asso') {
                $creatorOrOwner = \Scopes::getClient($request)->asso;
            }
        }

        if (!isset($creatorOrOwner)) {
            $creatorOrOwner = \Scopes::isClientToken($request) ? \Scopes::getClient($request) : \Auth::user();
        }

        if (!($creatorOrOwner instanceof CanHaveArticles)) {
            abort(400, 'La personne créatrice/possédeur doit au moins pouvoir avoir un article');
        }

        return $creatorOrOwner;
    }

    /**
     * Liste les articles.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (\Scopes::isOauthRequest($request)) {
            $articles = Article::getSelection()->filter(function ($article) use ($request) {
                return $this->tokenCanSee($request, $article, 'get')
                && (!\Auth::id() || $this->isVisible($article, \Auth::id()));
            });
        } else {
            $articles = Article::getSelection()->filter(function ($article) {
                return $this->isVisible($article);
            });
        }

        return response()->json($articles->values()->map(function ($article) {
            return $article->hideData();
        }));
    }

    /**
     * Créer un article.
     *
     * @param ArticleRequest $request
     * @return JsonResponse
     */
    public function store(ArticleRequest $request): JsonResponse
    {
        $inputs = $request->all();

        // On récupère pour qui c'est créé.
        $owner = $this->getCreatorOrOwner($request, 'create', 'owned');
        $ownerName = \ModelResolver::getNameFromObject($owner);

        // Le créateur peut être multiple: le user, l'asso ou le client courant. Ou autre.
        if ($request->input('created_by_type', 'user') === 'user'
	        && \Auth::id()
	        && $request->input('created_by_id', \Auth::id()) === \Auth::id()
	        && \Scopes::hasOne($request,
                \Scopes::getTokenType($request).'-create-articles-'.$ownerName.'s-owned-user')) {
            $creator = \Auth::user();
        } else if ($request->input('created_by_type', 'client') === 'client'
	        && $request->input('created_by_id', \Scopes::getClient($request)->id) === \Scopes::getClient($request)->id
	        && \Scopes::hasOne($request,
                \Scopes::getTokenType($request).'-create-articles-'.$ownerName.'s-owned-client')) {
            $creator = \Scopes::getClient($request);
        } else if ($request->input('created_by_type') === 'asso'
	        && $request->input('created_by_id', ($asso_id = \Scopes::getClient($request)->asso->id)) === $asso_id
	        && \Scopes::hasOne($request,
                \Scopes::getTokenType($request).'-create-articles-'.$ownerName.'s-owned-asso')) {
            $creator = \Scopes::getClient($request)->asso;
        } else {
            $creator = $this->getCreatorOrOwner($request, 'create', 'created');
        }

        $inputs['created_by_id'] = $creator->id;
        $inputs['created_by_type'] = get_class($creator);
        $inputs['owned_by_id'] = $owner->id;
        $inputs['owned_by_type'] = get_class($owner);

        if ($request->filled('event_id')) {
            // On fait vérifier que la personne à les droits sur l'event.
            $this->getEvent($request, \Auth::user(), $inputs['event_id']);
        }

        $article = Article::create($inputs);

        // On affecte l'image si tout s'est bien passé.
        $this->setImage($request, $article, 'articles', $article->id);

        // Tags.
        if ($request->has('tags') && is_array($inputs['tags'])) {
            $tags = Tag::all();

            foreach ($inputs['tags'] as $tag_arr) {
                if ($tag = $tags->firstWhere('name', $tag_arr['name'])) {
                    $article->tags()->save($tag);
                } else {
                    $tag = new Tag;
                    $tag->name = $tag_arr['name'];
                    $tag->description = array_key_exists("description", $tag_arr) ? $tag_arr['description'] : null;
                    $tag->save();
                    $article->tags()->save($tag);
                }
            }
        }

        $article = $this->getArticle($request, \Auth::user(), $article->id);

        return response()->json($article->hideSubData(), 201);
    }

    /**
     * Montre un article.
     *
     * @param Request $request
     * @param string  $article_id
     * @return JsonResponse
     */
    public function show(Request $request, string $article_id): JsonResponse
    {
        $article = $this->getArticle($request, \Auth::user(), $article_id);

        return response()->json($article->hideSubData());
    }

    /**
     * Met à jour un article.
     *
     * @param Request $request
     * @param string  $article_id
     * @return JsonResponse
     */
    public function update(Request $request, string $article_id): JsonResponse
    {
        $article = $this->getCalendar($request, \Auth::user(), $article_id, 'edit');
        $inputs = $request->all();

        if ($request->filled('owned_by_type')) {
            $owner = $this->getCreatorOrOwner($request, 'edit', 'owned');

            $inputs['owned_by_id'] = $owner->id;
            $inputs['owned_by_type'] = get_class($owner);
        }

        if ($request->filled('event_id')) {
            // On fait vérifier que la personne à les droits sur l'event.
            $this->getEvent($request, \Auth::user(), $inputs['event_id']);
        }

        if ($article->update($inputs)) {
            // On affecte l'image si tout s'est bien passé.
            $this->setImage($request, $article, 'articles', $article->id);

            // Tags.
            if ($request->has('tags') && is_array($inputs['tags'])) {
                $tags = Tag::all();

                foreach ($inputs['tags'] as $tag_arr) {
                    if (!$tags->firstWhere('name', $tag_arr['name'])) {
                        $tag = new Tag;
                        $tag->name = $tag_arr['name'];
                        $tag->description = array_key_exists("description", $tag_arr) ? $tag_arr['description'] : null;
                        $tag->save();
                        $article->tags()->save($tag);
                    } else {
                        $tag = Tag::where('name', $tag_arr['name'])->first();
                        $article->tags()->save($tag);
                    }
                }
            }

            $article = $this->getArticle($request, \Auth::user(), $article->id);

            return response()->json($article->hideData());
        } else {
            abort(500, 'Impossible de modifier l\'article');
        }
    }

    /**
     * Supprime un article.
     *
     * @param Request $request
     * @param string  $article_id
     * @return void
     */
    public function destroy(Request $request, string $article_id): void
    {
        $article = $this->getArticle($request, \Auth::user(), $article_id, 'remove');
        $article->tags()->delete();

        if ($article->delete()) {
            $this->deleteImage('articles/'.$article->id);

            abort(204);
        } else {
            abort(500, 'L\'article n\'a pas pu être supprimé');
        }
    }
}
