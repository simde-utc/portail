<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\Controller\v1\HasUsers;
use Encore\Admin\Layout\Content;
use Encore\Admin\Grid;
use Illuminate\Http\Request;
use App\Models\{
    User, AuthCas
};

class SearchController extends Controller
{
    use HasUsers;

    protected $fields = [
        'id', 'email', 'lastname', 'firstname', 'loginCAS'
    ];

    protected $limit;

    public function __construct() {
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
            ->description('Permet de rechercher un utilisateur (max. '.$this->limit.')')
            ->body(view('admin.search.index', ['fields' => $this->fields]));
    }

    /**
     * Affiche la liste des utilisateurs retrouvés.
     *
     * @param Request $request
     * @param Content $content
     * @return Content
     */
    public function search(Request $request, Content $content)
    {
        $grid = new Grid(new User());

        $filled = false;

        foreach ($this->fields as $field) {
            if ($request->filled($field)) {
                $value = '%'.$request->input($field).'%';
                $filled = true;

                if ($field === 'loginCAS') {
                    $cas = AuthCas::where('login', 'LIKE', $value)->get(['user_id'])
                        ->map(function ($cas) { return $cas->user_id; });

                    $grid->model()->whereIn('id', $cas);
                } else {
                    $grid->model()->where($field, 'LIKE', $value);
                }
            }
        }

        if (!$filled) {
            return back()->withErrors(['general' => 'Il est nécessaire de remplir au moins un champ']);
        }

        $grid->id();
        $grid->email();
        $grid->firstname();
        $grid->lastname();
        $grid->created_at();
        $grid->updated_at();
        $grid->types()->display(function () {
            $badges = '';

            foreach ($this->getTypes() as $type) {
                if ($this->isType($type)) {
                    $badges .= '<span class="badge">'.$type.'</span>';
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
            ->description('Permet de rechercher un utilisateur (max. '.$this->limit.')')
            ->body($grid);
    }

    public function show(string $user_id) {
        return redirect('admin/users/'.$user_id);
    }

    /**
     * Index interface.
     *
     * @param Request $request
     * @return mixed
     */
    public function impersonate(Request $request)
    {
        try {
            $user = $this->getUser($request, $request->input('user'), true);
        } catch (\Exception $e) {
            return back()->withErrors(['user' => 'L\'utilisateur n\'existe pas']);
        }

        $lastUser = \Auth::guard('web')->user();

        if ($lastUser->id === $user->id) {
            return back()->withErrors(['user' => 'Il n\'est pas possible de devenir soit-même']);
        }

        \Auth::guard('web')->login($user);

        return redirect('/');
    }
}
