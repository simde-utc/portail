<?php
/**
 * Permet de gérer les rôles utilisateurs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Pivots\UserRole;
use App\Models\{
	User, Semester, Role
};
use Encore\Admin\Layout\Content;
use App\Admin\GridGenerator;
use Encore\Admin\Grid\Displayers\Actions;
use Illuminate\Http\Request;
use App\Notifications\Admin\MemberAccessValidation;

class UserRoleController extends Controller
{
    protected $model = UserRole::class;

    /**
     * Interface d'affichage global.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $grid = new GridGenerator($this->model);
        $userRoles = Role::where('owned_by_type', User::class)->get();
        $grid::$simplePrint = true;

        $grid->addFields([
            'id' => 'display',
            'user' => User::get(['id', 'firstname', 'lastname']),
            'role' => Role::get(['id', 'name']),
            'semester' => Semester::get(['id', 'name']),
            'validated_by' => User::get(['id', 'firstname', 'lastname']),
            'created_at' => 'display',
            'updated_at' => 'display'
        ]);

        $grid->tools(function ($tools) {
            $tools->disableBatchActions();
        });

        // $grid->disableCreation();
        $grid->disableExport();

        $grid->actions(function (Actions $actions) use ($userRoles) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();

            $row = $actions->row;

            $generateAction = function ($view) use ($actions, $row, $userRoles) {
                $actions->append(view($view, [
                    'data' => $row,
                    'roles' => $userRoles,
                    'ids' => [$row->user_id, $row->semester_id],
                ])->__toString());
            };

            if ($row->validated_by === null) {
                $generateAction('admin.users.role.validate');
            }

            $generateAction('admin.users.role.edit');
            $generateAction('admin.users.role.delete');
        });

        $grid->get()->model()
        ->whereNotNull('role_id')
        ->orderBy('created_at', 'DESC');

        return $content
            ->header('Gestion des rôles utilisateurs')
            ->description('Permet d\'ajouter, modifier, supprimer, un rôle utilisateur')
            ->body($grid->get());
    }

    /**
     * Valide le membre.
     *
     * @param  Request $request
     * @param  string  $member_id
     * @param  string  $semester_id
     * @return mixed
     */
    public function store(Request $request, string $member_id, string $semester_id)
    {
        UserRole::where('user_id', $member_id)
            ->where('semester_id', $semester_id)
            ->whereNull('validated_by_id')
            ->update(['validated_by_id' => \Auth::guard('admin')->id()]);

        return back();
    }

    /**
     * Modifie les informations du membre.
     *
     * @param  Request $request
     * @param  string  $member_id
     * @param  string  $semester_id
     * @return mixed
     */
    public function update(Request $request, string $member_id, string $semester_id)
    {
        if ($request->input('role_id')) {
            $member = UserRole::where('user_id', $member_id)
                ->where('semester_id', $semester_id)
                ->update(['role_id' => $request->input('role_id'), 'validated_by_id' => \Auth::guard('admin')->id()]);
        }

        return back();
    }

    /**
     * Supprime le membre.
     *
     * @param  Request $request
     * @param  string  $member_id
     * @param  string  $semester_id
     * @return mixed
     */
    public function delete(Request $request, string $member_id, string $semester_id)
    {
        $member = UserRole::where('user_id', $member_id)
        ->where('semester_id', $semester_id)
        ->delete();

        return back();
    }
}
