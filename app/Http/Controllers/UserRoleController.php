<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Semester;
use App\Http\Requests\UserRequest;
use App\Services\Visible\Visible;
use App\Models\Visibility;
use App\Exceptions\PortailException;

class UserRoleController extends Controller
{
    public function __construct() {
		$this->middleware(
			\Scopes::matchOne(
				['user-get-assos-joined-now', 'user-get-assos-followed-now']
			),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['user-create-assos']
			),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['user-set-assos']
			),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['user-manage-assos']
			),
			['only' => ['destroy']]
		);
    }

	protected function getUser(Request $request, int $user_id = null) {
		$user = User::find($user_id ?? \Auth::id());

		if ($user)
			return $user;
		else
			abort(404, "Utilisateur non trouvé");
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, int $user_id = null) {
		$user = $this->getUser($request, $user_id);

		return response()->json($user->roles()->wherePivot('semester_id', Semester::getSemester($request->input('semester'))->id ?? Semester::getThisSemester()->id)->withPivot('semester_id', 'validated_by')->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $user_id = null) {
		$user = $this->getUser($request, $user_id);

		if (\Scopes::isUserToken($request)) {
            $inputs = $request->input();
            $role_id = $inputs['role_id'];
            unset($inputs['role_id']);

            $inputs['validated_by'] = \Auth::id();

			$role = $user->assignRoles($request->input('role_id'), $inputs)->roles()->wherePivot('role_id', $role_id)->withPivot('semester_id', 'validated_by');

            foreach ($inputs as $name => $value)
                $role = $role->wherePivot($name, $value);

            return response()->json($role->first());
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $user_id, int $role_id = null) {
        if (is_null($role_id))
            list($user_id, $role_id) = [$role_id, $user_id];

        $user = $this->getUser($request, $user_id);
        $role = $user->roles()->wherePivot('role_id', $role_id)->wherePivot('semester_id', Semester::getSemester($request->input('semester'))->id ?? Semester::getThisSemester()->id)->withPivot('semester_id', 'validated_by')->first();

		if ($role)
			return response()->json($role);
		else
			abort(404, 'Cette personne ne possède pas ce rôle');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $user_id, int $role_id = null) {
		abort(405, 'Impossible de modifier l\'assognation d\'un rôle');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $user_id, int $role_id = null) {
        if (is_null($role_id))
            list($user_id, $role_id) = [$role_id, $user_id];

        $user = $this->getUser($request, $user_id);
        $role = $user->roles()->wherePivot('role_id', $role_id)->wherePivot('semester_id', Semester::getSemester($request->input('semester'))->id ?? Semester::getThisSemester()->id)->withPivot('semester_id', 'validated_by')->first();

        if (!$role)
            abort(404, 'Cette personne ne possède pas ce rôle');

		if (\Scopes::isUserToken($request)) {
            $user->removeRoles($role_id, [
                'semester_id' => Semester::getSemester($request->input('semester'))->id ?? Semester::getThisSemester()->id,
            ], \Auth::id());

            abort(204);
		}
    }
}
