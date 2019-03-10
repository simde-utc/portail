<?php
/**
 * Permet de chercher un utilisateur.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\Controller\v1\HasUsers;
use Encore\Admin\Layout\Content;
use Encore\Admin\Grid;
use Illuminate\Http\Request;
use App\Models\{
    User, AuthCas
};

class SearchUserController extends Controller
{
    use HasUsers;

    protected $fields = [
        'id', 'email', 'lastname', 'firstname', 'loginCAS'
    ];

    protected $limit;

    /**
     * Donne l'accès uniquement si la personne possède la permission.
     * Récupération de la limite.
     */
    public function __construct()
    {
        $this->middleware('permission:user');

        $this->limit = config('admin.extensions.search.limit');
    }

    /**
     * Retourne le formulaire de recherche.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Recherche d\'un utilisateur')
            ->description('Permet de rechercher un utilisateur (max. '.$this->limit.' en même temps)')
            ->body(view('admin.search.user.index', ['fields' => $this->fields]));
    }

    /**
     * Affiche la liste des utilisateurs retrouvés.
     *
     * @param Request $request
     * @param Content $content
     * @return mixed
     */
    public function search(Request $request, Content $content)
    {
        $grid = new Grid(new User());

        if ($request->filled('any')) {
            $value = '%'.$request->input('any').'%';

            foreach ($this->fields as $field) {
                if ($field === 'loginCAS') {
                    $cas = AuthCas::where('login', 'LIKE', $value)->get(['user_id'])->pluck('user_id')->toArray();

                    $grid->model()->orWhereIn('id', $cas);
                } else {
                    $grid->model()->orWhere($field, 'LIKE', $value);
                }
            }
        } else {
            $filled = false;

            foreach ($this->fields as $field) {
                if ($request->filled($field)) {
                    $value = '%'.$request->input($field).'%';
                    $filled = true;

                    if ($field === 'loginCAS') {
                        $cas = AuthCas::where('login', 'LIKE', $value)->get(['user_id'])->pluck('user_id')->toArray();

                        $grid->model()->whereIn('id', $cas);
                    } else {
                        $grid->model()->where($field, 'LIKE', $value);
                    }
                }
            }

            if (!$filled) {
                return back()->withErrors(['general' => 'Il est nécessaire de remplir au moins un champ']);
            }
        }

        $grid->id();
        $grid->email();
        $grid->firstname();
        $grid->lastname();
        $grid->last_login_at();
        $grid->created_at();
        $grid->updated_at();
        $grid->types()->display(function () {
            $badges = '';

            foreach ($this->getTypeDescriptions() as $type => $description) {
                if ($this->isType($type)) {
                    $badges .= '<span class="badge">'.$description.'</span>';
                }
            }

            return $badges;
        });

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });

        $grid->disableCreateButton();
        $grid->disableFilter();
        $grid->disableRowSelector();
        $grid->disablePagination();
        $grid->disableExport();

        $grid->model()->orderBy('email')->take($this->limit);

        return $content
            ->header('Liste des utilisateurs trouvés')
            ->description('Permet de rechercher un utilisateur (max. '.$this->limit.' en même temps)')
            ->body($grid);
    }

    /**
     * Retourne l'affichage de l'utlisateur.
     *
     * @param  Content $content
     * @param  string  $user_id
     * @return mixed
     */
    public function show(Content $content, string $user_id)
    {
        return (new Resource\UserController)->show($content, $user_id);
    }
}
