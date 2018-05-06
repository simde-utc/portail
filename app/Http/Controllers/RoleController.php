<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Group;
use App\Models\Semester;
use App\Http\Requests\GroupRequest;
use App\Models\Role;
use App\Exceptions\PortailException;
use App\Traits\HasStages;

/**
 * Gestion des groupes utilisateurs
 *
 * @resource Group
 */
class RoleController extends Controller
{
	use HasStages;
	/**
	 * Scopes Group
	 *
	 * Les Scopes requis pour manipuler les Groups
	 */
	public function __construct() {
		$this->middleware(
			\Scopes::matchOne(
				['user-get-groups-enabled', 'user-get-groups-disabled'],
				['client-get-groups-enabled', 'client-get-groups-disabled']
			),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['user-manage-groups']
			),
			['only' => ['store', 'update', 'destroy']]
		);
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
		if (isset($request['stage']) || isset($request['fromStage']) || isset($request['toStage']) || isset($request['allStages'])) {
	        // On inclue les relations et on les formattent.
			$inputs = $request->input();
			unset($inputs['stage']);
			unset($inputs['fromStage']);
			unset($inputs['toStage']);
			unset($inputs['allStages']);

			$roles = isset($request['stage']) ? Role::getStage($request->stage, $inputs) : Role::getStages($request->fromStage, $request->toStage, $inputs);
		}
		else
			$roles = Role::get();

		return response()->json($roles, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupRequest $request) {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
		if (is_numeric($id))
			$role = Role::find($id);
		else
			$role = Role::where('login', $id)->first();

        if ($role) {
			$role->nbr_assigned = $role->users()->where('semester_id', Semester::getThisSemester()->id)->count();

			return response()->json($role, 200);
		}
        else
            abort(404, "Role non trouvé");
    }

	/**
	 * Update Group
	 *
	 * @param  \Illuminate\Http\GroupRequest  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(GroupRequest $request, $id) {
    }

	/**
	 * Delete Group
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
    }
}
