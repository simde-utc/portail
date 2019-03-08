<?php
/**
 * Permet de chercher un utilisateur sur Ginger.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\Controller\v1\HasUsers;
use Encore\Admin\Layout\{
	Content, Row, Column
};
use Encore\Admin\{
	Grid, Show
};
use Illuminate\Http\Request;
use App\Admin\GridGenerator;
use App\Models\{
    User, AuthCas, Semester
};

class SearchContributorController extends Controller
{
    use HasUsers;

    /**
     * Retourne le formulaire de recherche.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Recherche d\'un utilisateur cotisant')
            ->description('Permet de rechercher un cotisant ou de le faire cotiser')
            ->body(view('admin.search.contributor.index'));
    }

    /**
     * Retourne l'affichage de l'utlisateur.
     *
     * @param  Request $request
     * @param  Content $content
     * @param  string  $login
     * @return mixed
     */
    public function show(Request $request, Content $content, string $login)
    {
        $content = $content
            ->header('Recherche d\'un utilisateur cotisant')
            ->description('Permet de rechercher un cotisant ou de le faire cotiser')
            ->row(view('admin.search.contributor.index', ['login' => $login]));

        $user = \Ginger::user($login);

        if ($user->exists()) {
            $columnPicture = function (Column $column) use ($user) {
                $this->generatePicture($column, $user);
            };
            $userInfo = function (Column $column) use ($user) {
                $this->generateInfo($column, $user);
            };
            $contributions = function (Column $column) use ($user) {
                $this->generateContributions($column, $user);
            };

            $content->row(function (Row $row) use ($columnPicture, $userInfo) {
                $row->column(2, null);
                $row->column(3, $columnPicture);
                $row->column(5, $userInfo);
            });

                  $content->row(function (Row $row) use ($contributions) {
                    $row->column(2, null);
                      $row->column(8, $contributions);
                  });
        }

        return $content;
    }

    /**
     * Affiche l'image de la personne.
     *
     * @param  Column $column
     * @param  mixed  $user
     * @return mixed
     */
    protected function generatePicture(Column $column, $user)
    {
        $image = new Show(new User([
            'image' => config('portail.cas.image').$user->getLogin(),
        ]));
        $image->image(' ')->image();
        $image->panel()
            ->title('Image')
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();
            });

        $column->row($image);
    }

    /**
     * Affiche les informations de la personne.
     *
     * @param  Column $column
     * @param  mixed  $user
     * @return mixed
     */
    protected function generateInfo(Column $column, $user)
    {
        $userModel = new User([
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
        ]);
        $userModel->login = $user->getLogin();

        $info = new Show($userModel);

        $info->login();
        $info->email();
        $info->firstname('Prénom');
        $info->lastname('Nom');
        $info->panel()
            ->title('Informations')
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();
            });

        $column->row($info);
    }

    /**
     * Affiche les cotisations de la personne.
     *
     * @param  Column $column
     * @param  mixed  $user
     * @return mixed
     */
    protected function generateContributions(Column $column, $user)
    {
        $userModel = new User([
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
        ]);
        $userModel->id = $user->getLogin();
        $userModel->login = $user->getLogin();

        $contributeBde = new Show($userModel);
        $contributions = [];

        foreach (array_reverse($user->getContributions()) as $contribution) {
            $semesters = Semester::whereDate('begin_at', '<', $contribution->end_at)
                ->whereDate('end_at', '>', $contribution->begin_at)
                ->orderBy('begin_at')->get(['name']);

            $name = implode($semesters->pluck('name')->toArray(), '-');

            if (!isset($contributions[$name])) {
                $contributions[$name] = [];
            }

            $contributions[$name][] = [
                'début' => $contribution->begin_at,
                'fin' => $contribution->end_at,
                'montant' => $contribution->money.'€',
            ];
        }

        foreach ($contributions as $key => $value) {
            $contributeBde->$key($key)->unescape()->as(function () use ($value) {
                return GridGenerator::adminValue($value);
            });
        }

        $contributeBde->panel()
            ->title('Cotisation BDE')
            ->tools(function ($tools) use ($user, $userModel) {
                $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();

                if ($user->isContributor()) {
                    $tools->prepend(view('admin.users.contributeBDE.delete', ['user' => $userModel]));
                } else {
                    $tools->prepend(view('admin.users.contributeBDE.create', ['user' => $userModel]));
                }
            });

        $column->row($contributeBde);
    }
}
