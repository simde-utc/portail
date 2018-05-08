<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Role;
use App\Models\Semester;
use App\Exceptions\PortailException;
use App\Traits\HasStages;

/**
 * Gestion des groupes utilisateurs
 *
 * @resource Role
 */
class RoleController extends Controller
{
	use HasStages;
	/**
	 * Scopes Role
	 *
	 * Les Scopes requis pour manipuler les Roles
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
			array_merge(
				\Scopes::matchOne(
					['user-manage-groups']
				), [
					'admin',
				]
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
		$inputs = $request->input();

		if ($request->has('stage') || $request->has('fromStage') || $request->has('toStage') || $request->has('allStages')) {
	        // On inclue les relations et on les formattent.
			unset($inputs['stage']);
			unset($inputs['fromStage']);
			unset($inputs['toStage']);
			unset($inputs['allStages']);

			$roles = $request->has('stage') ? Role::getStage($request->stage, $inputs) : Role::getStages($request->fromStage, $request->toStage, $inputs);
		}
		else {
			$roles = new Role;

			foreach ($inputs as $key => $value) {
				if (!\Schema::hasColumn($roles->getTable(), $key))
					throw new PortailException('L\'attribut '.$key.' n\'existe pas');

				$roles = $roles->where($key, $value);
			}

			$roles = $roles->get();
		}

		return response()->json($roles, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
		$role = new Role;
		$role->type = $request->type;
		$role->name = $request->name;
		$role->description = $request->description;
		$role->limited_at = $request->limited_at;

		@list($tableName, $id) = explode('_', $request->only_for);
		$class = '\\App\\Models\\'.studly_case(str_singular($tableName));

		if (!class_exists($class))
			abort(404, 'La table donnée pour only_for n\'existe pas !');
		else if (!in_array('App\\Traits\\HasRoles', class_uses($class)))
			abort(400, 'La table donnée ne possède pas de roles');
		else if ($id && !resolve($class)->find($id))
			abort(400, 'L\'id associé à only_for n\'existe pas ');

		$role->only_for = $request->only_for;

		if ($role->save()) {
			if ($request->filled('parent_ids'))
				$role->assignParentRole($request->parent_ids);

			return response()->json($role, 201);
		}
		else
			abort(500, 'Impossible de créer le role');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
		$role = $request->has('withChilds') ? Role::with('childs') : new Role;
		$role = $request->has('withParents') ? $role->with('parents') : $role;
		$role = is_numeric($id) ? $role->find($id) : $role->where('type', $id)->first();

        if ($role) {
			$role->nbr_assigned = $role->users()->where('semester_id', Semester::getThisSemester()->id)->count();

			return response()->json($role, 200);
		}
        else
            abort(404, "Role non trouvé");
    }

	/**
	 * Update Role
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		$role = $request->has('withChilds') ? Role::with('childs') : new Role;
		$role = $request->has('withParents') ? $role->with('parents') : $role;
		$role = is_numeric($id) ? $role->find($id) : $role->where('type', $id)->first();

		if ($request->filled('type'))
			$role->type = $request->input('type');

		if ($request->filled('name'))
			$role->name = $request->input('name');

		if ($request->filled('description'))
			$role->description = $request->input('description');

		if ($request->filled('limited_at'))
			$role->limited_at = $request->input('limited_at');

		if ($request->filled('only_for')) {
			@list($tableName, $id) = explode('_', $request->input('only_for'));
			$class = '\\App\\Models\\'.studly_case(str_singular($tableName));

			if (!class_exists($class))
				abort(404, 'La table donnée pour only_for n\'existe pas !');
			else if (!in_array('App\\Traits\\HasRoles', class_uses($class)))
				abort(400, 'La table donnée ne possède pas de roles');
			else if ($id && !resolve($class)->find($id))
				abort(400, 'L\'id associé à only_for n\'existe pas ');

			$role->only_for = $request->input('only_for');
		}

		if ($role->save()) {
			if ($request->filled('parent_ids')) {
				$role->syncParentRole($request->parent_ids); // Attention ! Ici on change tous ses parents

				$role = $request->has('withChilds') ? $role->with('childs') : $role;
				$role = $request->has('withParents') ? $role->with('parents') : $role;
				$role = is_numeric($id) ? $role->find($id) : $role->where('type', $id)->first();
			}

			return response()->json($role, 200);
		}
		else
			abort(500, 'Impossible de créer le role');
    }

	/**
	 * Delete Role
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $id) {
		if (is_numeric($id))
			$role = Role::find($id);
		else
			$role = Role::where('type', $id)->first();

	    if (!$role || !$role->delete())
			abort(404, "Role non trouvé");
		else
			abort(204);
    }
}
