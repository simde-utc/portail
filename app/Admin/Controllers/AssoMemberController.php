<?php
/**
 * Permet de gérer les membres associatifs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Pivots\AssoMember;
use App\Models\{
	Asso, User, Semester, Role
};
use Encore\Admin\Layout\Content;
use App\Admin\GridGenerator;
use Encore\Admin\Grid\Displayers\Actions;
use Illuminate\Http\Request;
use App\Notifications\Admin\MemberAccessValidation;

class AssoMemberController extends Controller
{
    protected $model = AssoMember::class;

    /**
     * Interface d'affichage global.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $grid = new GridGenerator($this->model);
        $assosRoles = [];
        $grid::$simplePrint = true;

        $grid->addFields([
            'id' => 'display',
            'user' => User::get(['id', 'firstname', 'lastname']),
            'role' => Role::get(['id', 'name']),
            'asso' => Asso::get(['id', 'name']),
            'semester' => Semester::get(['id', 'name']),
            'validated_by' => User::get(['id', 'firstname', 'lastname']),
            'created_at' => 'display',
            'updated_at' => 'display'
        ]);

        $grid->tools(function ($tools) {
            $tools->disableBatchActions();
        });

        $grid->disableCreation();
        $grid->disableExport();

        $grid->actions(function (Actions $actions) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();

            $row = $actions->row;
            $roles = ($assosRoles[$row->asso_id] ?? ($assosRoles[$row->asso_id] = $row->asso->getUserRoles()));

            $generateAction = function ($view) use ($actions, $row, $roles) {
                $actions->append(view($view, [
                    'member' => $row,
                    'roles' => $roles,
                    'ids' => [$row->asso_id, $row->user_id, $row->semester_id],
                ])->__toString());
            };

            if ($row->validated_by === null) {
                $generateAction('admin.asso.member.validate');
            }

            $generateAction('admin.asso.member.edit');
            $generateAction('admin.asso.member.delete');
        });

        $grid->get()->model()
        ->whereNotNull('role_id')
        ->orderBy('created_at', 'DESC');

        return $content
            ->header('Gestion des membres associatifs')
            ->description('Permet d\'accepter, modifier et supprimer les membres')
            ->body($grid->get());
    }

    /**
     * Valide le membre.
     *
     * @param  Request $request
     * @param  string  $asso_id
     * @param  string  $member_id
     * @param  string  $semester_id
     * @return mixed
     */
    public function store(Request $request, string $asso_id, string $member_id, string $semester_id)
    {
        AssoMember::where('asso_id', $asso_id)
        ->where('user_id', $member_id)
        ->where('semester_id', $semester_id)
        ->whereNull('validated_by_id')
        ->update(['validated_by_id' => \Auth::guard('admin')->id()]);

        return back();
    }

    /**
     * Modifie les informations du membre.
     *
     * @param  Request $request
     * @param  string  $asso_id
     * @param  string  $member_id
     * @param  string  $semester_id
     * @return mixed
     */
    public function update(Request $request, string $asso_id, string $member_id, string $semester_id)
    {
        if ($request->input('role_id')) {
            $member = AssoMember::where('asso_id', $asso_id)
            ->where('user_id', $member_id)
            ->where('semester_id', $semester_id)
            ->update(['role_id' => $request->input('role_id'), 'validated_by_id' => \Auth::guard('admin')->id()]);
        }

        return back();
    }

    /**
     * Supprime le membre.
     *
     * @param  Request $request
     * @param  string  $asso_id
     * @param  string  $member_id
     * @param  string  $semester_id
     * @return mixed
     */
    public function delete(Request $request, string $asso_id, string $member_id, string $semester_id)
    {
        $member = AssoMember::where('asso_id', $asso_id)
        ->where('user_id', $member_id)
        ->where('semester_id', $semester_id)
        ->delete();

        return back();
    }
}
