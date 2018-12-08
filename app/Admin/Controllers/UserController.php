<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\Controller\v1\HasUsers;
use Encore\Admin\Grid\Filter;
use Encore\Admin\Layout\{
    Content, Column, Row
};
use Encore\Admin\{
    Grid, Show, Form
};
use Illuminate\Http\Request;
use App\Models\{
    User, AuthCas
};
use App\Notifications\Admin\{
    AdminImpersonation, UserImpersonation
};

class UserController extends Controller
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
        $grid = new Grid(new User());

        $grid->id()->sortable();
        $grid->email()->sortable();
        $grid->firstname()->sortable();
        $grid->lastname()->sortable();
        $grid->last_login_at()->sortable();
        $grid->created_at()->sortable();
        $grid->updated_at()->sortable();
        $grid->types()->display(function () {
            $badges = '';

            foreach ($this->getTypes() as $type) {
                if ($this->isType($type)) {
                    $badges .= '<span class="badge">'.$type.'</span>';
                }
            }

            return $badges;
        });

        $grid->model()->orderBy('created_at');

        $grid->filter(function (Filter $filter) {
            $filter->like('email');
            $filter->like('firstname');
            $filter->like('lastname');
        });

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });

        return $content
            ->header('Liste des utilisateurs')
            ->description('Affiche les derniers utilisateurs par défaut')
            ->body($grid);
    }

    public function show(Content $content, string $user_id)
    {
        $user = User::findOrFail($user_id);
        $image = new Show($user);
        $image->image(' ')->image();
        $image->panel()
            ->title('Image')
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();
            });

        $details = new Show($user);
        $details->id();
        $details->email();
        $details->firstname();
        $details->lastname();
        $details->last_login_at();
        $details->created_at();
        $details->updated_at();
        $details->types()->unescape()->as(function () {
            $badges = '';

            foreach ($this->getTypes() as $type) {
                if ($this->isType($type)) {
                    $badges .= '<span class="badge">'.$type.'</span>';
                }
            }

            return $badges;
        });

        $generateLeft = function (Column $column) use ($user, $image) {
            $column->row($image);
        };

        $generateRight = function (Column $column) use ($user, $details) {
            if (!\Auth::guard('admin')->user()->can('user')) {
                $column->row($details);
            } else {
                if (\Auth::guard('admin')->user()->can('user-impersonate')) {
                    $details->panel()
                        ->tools(function ($tools) use ($user) {
                            $tools->prepend(view('admin.users.impersonate', ['user' => $user]));
                        });
                }

                $column->row($details);

                $info = new Show($user);
                $info->details()->unescape()->as(function () {
                    return arrayToTable($this->details()->allToArray());
                });
                $info->preferences()->unescape()->as(function () {
                    return arrayToTable($this->preferences()->allToArray());
                });

                $info->panel()
                    ->title('Information')
                    ->tools(function ($tools) {
                        $tools->disableEdit();
                        $tools->disableList();
                        $tools->disableDelete();
                    });

                $column->row($info);
            }
        };

        return $content
            ->header($user->name)
            ->description('Détail de l\'utilisateur')
            ->row(function (Row $row) use ($user, $generateLeft, $generateRight) {
                $row->column(3, $generateLeft);

                $row->column(9, $generateRight);
            });
    }

    /**
     * Index interface.
     *
     * @param Request $request
     * @return mixed
     */
    public function impersonate(Request $request, string $user_id)
    {
        if (!$request->filled('description') || !\Auth::guard('admin')->user()->can('user-impersonate')) {
            return redirect()->action(
                '\App\Admin\Controllers\UserController@show', ['user_id' => $user_id]
            );
        }

        try {
            $user = $this->getUser($request, $user_id, true);
        } catch (\Exception $e) {
            return redirect()->action(
                '\App\Admin\Controllers\UserController@index'
            );
        }

        $admin = \Auth::guard('admin')->user();

        if ($admin->id === $user->id) {
            return redirect()->action(
                '\App\Admin\Controllers\UserController@show', ['user_id' => $user_id]
            );
        }

        // Envoi de la notification pour confirmer le changement
        $userNotification = new UserImpersonation($admin, $request->input('description'), (bool) $request->input('admin'));

        $user->notify($userNotification);

        \Auth::guard('web')->login($user);

        if ($request->input('admin')) {
            \Auth::guard('admin')->login(\App\Admin\Models\Admin::find($user->id));
        }

        return redirect('/');
    }
}
