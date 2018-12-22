<?php
/**
 * Permet de gérer les accès.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{
    AssoAccess, Asso, User, Access, Semester, Role
};
use Encore\Admin\Layout\Content;
use App\Admin\GridGenerator;
use Encore\Admin\Grid\Displayers\Actions;

class AccessController extends Controller
{
    protected $model = AssoAccess::class;

    /**
     * Interface d'affichage global.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $grid = new GridGenerator($this->model);

        $grid->addFields([
            'id' => 'display',
            'asso' => Asso::get(['id', 'name']),
            'access' => Access::get(['id', 'name']),
            'semester' => Semester::get(['id', 'name']),
        ]);

        $addRoleToMember = function ($member) {
            $user = Asso::find($this->asso_id)->currentMembers()->wherePivot('user_id', $member['id'])->first();

            if ($user) {
                $role = Role::find($user->pivot->role_id, $this->asso);

                $member['pivot'] = GridGenerator::reduceModelArray($role->toArray());
            }

            return GridGenerator::arrayToTable(GridGenerator::reduceModelArray($member));
        };

        $grid->get()->member()->sortable()->display($addRoleToMember);
        $grid->get()->confirmed_by()->sortable()->display($addRoleToMember);

        $grid->addFields([
            'validated_by' => User::get(['id', 'firstname', 'lastname']),
            'description' => 'textarea',
            'comment' => 'text',
            'validated_at' => 'datetime',
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
        });

        $grid->get()->model()->orderBy('created_at', 'DESC');

        return $content
            ->header('Index')
            ->description('description')
            ->body($grid->get());
    }
}
