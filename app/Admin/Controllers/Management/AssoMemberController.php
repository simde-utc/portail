<?php
/**
 * Manage Associations Members.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Noé Amiot <noe.amiot@etu.utc.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Management;

use App\Admin\Controllers\Controller;
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
     * Give access only if user has the right permissions.
     */
    public function __construct()
    {
        $this->middleware('permission:handle-assos-members');
    }

    /**
     * Global display interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $grid = new GridGenerator($this->model);
        $assosRoles = [];
        $grid::$simplePrint = true;

        // Add all filters.
        $grid->filter(function($filter) {
            $filter->where(function ($query) {
                if (isset($this->input)) {
                    $query->whereHas('asso', function ($query) {
                        $query->where('id', '=', $this->input);
                    });
                }
            }, 'Asso')->select(Asso::get(['id', 'shortname'])->pluck('shortname', 'id'));

            $filter->where(function ($query) {
                if (isset($this->input)) {
                    $query->whereHas('user', function ($query) {
                        $query->whereRaw('lower(lastname) like ?', [strtolower("%{$this->input}%")]);
                    });
                }
            }, 'Nom du membre');
            $filter->where(function ($query) {
                if (isset($this->input)) {
                    $query->whereHas('user', function ($query) {
                        $query->whereRaw('lower(firstname) like ?', [strtolower("%{$this->input}%")]);
                    });
                }
            }, 'Prénom du membre');

            $filter->where(function ($query) {
                if (isset($this->input)) {
                    $query->whereHas('role', function ($query) {
                        $query->where('id', '=', $this->input);
                    });
                }
            }, 'Rôle')->select(Role::get(['id', 'name'])->pluck('name', 'id'));

            $filter->where(function ($query) {
                if (isset($this->input)) {
                    $query->whereHas('semester', function ($query) {
                        $query->where('id', '=', $this->input);
                    });
                }
            }, 'Semestre')->select(Semester::get(['id', 'name'])->pluck('name', 'id'));
        });

        $grid->column('user.lastname', 'Membre')->display(function () {
            if (isset($this->user)) {
                return $this->user->lastname." ".$this->user->firstname;
            }
        });

        $grid->column('asso.shortname', 'Asso');
        $grid->column('role.name', 'Rôle');
        $grid->column('semester.name', 'Semestre');
        $grid->column('validated_by.lastname', 'Validé par')->display(function () {
            if (isset($this->validated_by)) {
                return $this->validated_by->lastname." ".$this->validated_by->firstname;
            }
        });

        $grid->addFields([
            'created_at' => 'display',
            'updated_at' => 'display',
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

            // TODO: This is loading massive amount of data as each line displayed loads all the roles in a select form (and the data is always the same).
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
     * Cofirm member.
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
     * Modify a member information.
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
     * Delete membership.
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
