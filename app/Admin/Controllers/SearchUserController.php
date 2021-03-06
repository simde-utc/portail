<?php
/**
 * Allow to search for a user.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Noé Amiot <noe.amiot@etu.utc.fr>
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
        'email' => 'email',
        'lastname' => "nom",
        'firstname' => "prénom",
        'loginCAS' => "login CAS"
    ];

    protected $limit;

    /**
     * Give access only if the user has the right permission.
     * Limit retrievement.
     */
    public function __construct()
    {
        $this->middleware('permission:user');

        $this->limit = config('admin.extensions.search.limit');
    }

    /**
     * Return search form.
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
     * Display found user list.
     *
     * @param Request $request
     * @param Content $content
     * @return mixed
     */
    public function search(Request $request, Content $content)
    {
        $grid = new Grid(new User());

        if ($request->filled('quick_search')) {
            $values = explode(" ", $request->input('quick_search'));

            foreach ($values as $value) {
                foreach (array_keys($this->fields) as $field) {
                    if ($field === 'loginCAS') {
                        $cas = AuthCas::whereRaw(
                            'login LIKE convert(? using utf8mb4) COLLATE utf8mb4_general_ci',
                            [$value]
                        )->get(['user_id'])->pluck('user_id')->toArray();

                        $grid->model()->orWhereIn('id', $cas);
                    } else {
                        $grid->model()->orwhereRaw(
                            "$field LIKE convert( ? using utf8mb4) COLLATE utf8mb4_general_ci",
                            [$value]
                        );
                    }
                }
            }
        } else {
            $filled = false;

            foreach (array_keys($this->fields) as $field) {
                if ($request->filled($field)) {
                    $value = '%'.$request->input($field).'%';
                    $filled = true;

                    if ($field === 'loginCAS') {
                        $cas = AuthCas::whereRaw(
                            'login LIKE convert(? using utf8mb4) COLLATE utf8mb4_general_ci',
                            [$value]
                        )->get(['user_id'])->pluck('user_id')->toArray();
                        $grid->model()->whereIn('id', $cas);
                    } else {
                        $grid->model()->whereRaw("$field LIKE convert(? using utf8mb4) COLLATE utf8mb4_general_ci", [$value]);
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
     * Return user display.
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
