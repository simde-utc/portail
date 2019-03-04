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
use Illuminate\Http\Request;
use App\Notifications\Admin\MemberAccessValidation;

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
        $grid::$simplePrint = true;

        $grid->addFields([
            'id' => 'display',
            'asso' => Asso::get(['id', 'name']),
            'member' => User::get(['id', 'firstname', 'lastname']),
        ]);

        $grid->get()->role()->sortable()->display(function () {
			$asso = Asso::find($this->asso_id);

			if ($asso) {
				$user = $asso->currentMembers()->wherePivot('user_id', $this->member['id'])->first();

				if ($user) {
					$this->role = Role::find($user->pivot->role_id, $this->asso);

					return GridGenerator::modelToTable($this->role->toArray());
				}
			}
        });

		$grid->get()->acces('Accès')->sortable()->display(function () {
			$access = $this->access;

			return $access->name.' ('.$access->utc_access.')';
		});

        $grid->addFields([
            'semester' => Semester::get(['id', 'name']),
            'description' => 'textarea',
            'comment' => 'text',
            'validated_by' => User::get(['id', 'firstname', 'lastname']),
            'validated' => 'switch',
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

            $row = $actions->row;

            $generateAction = function ($view) use ($actions, $row) {
                $actions->append(view($view, ['access' => $row])->__toString());
            };

            if ($row->confirmed_by) {
                if ($row->validated_by) {
                    if ($row->validated) {
                        $generateAction('admin.access.validated');
                    } else {
                        $generateAction('admin.access.refused');
                    }
                } else {
                    $generateAction('admin.access.validate');
                }
            } else {
                $generateAction('admin.access.unconfirmed');
            }
        });

        $grid->get()->model()->orderBy('created_at', 'DESC');

        return $content
            ->header('Gestion des accès')
            ->description('Permet de savoir ce qui est validé')
            ->body($grid->get());
    }

    /**
     * Sauvegarde du changement de la demande d'accès.
     *
     * @param  Request $request
     * @param  string  $accessId
     * @return mixed
     */
    public function store(Request $request, string $accessId)
    {
        if ($request->filled('validate') && $request->filled('comment')) {
            $access = AssoAccess::find($accessId);

            if ($access && $access->confirmed_by && is_null($access->validated_by)) {
                $admin = \Auth::guard('admin')->user();

                $access->comment = $request->input('comment');
                $access->validated_by_id = $admin->id;
                $access->validated_at = now();
                $access->validated = $request->input('validate');

                $notif = new MemberAccessValidation($access, $admin);
                $access->save();

                $access->member->notify($notif);
            }
        }

        return back();
    }
}
