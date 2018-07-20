<?php

namespace App\Http\Controllers\v1\Asso;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Asso;
use App\Models\Semester;
use App\Models\Role;
use App\Exceptions\PortailException;
use Illuminate\Support\Collection;
use App\Traits\Controller\v1\HasAssos;

class MemberController extends Controller
{
	use HasAssos;

	public function __construct() {
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-get-assos', 'client-get-assos'), // Pouvoir voir les assos
				\Scopes::matchOneOfDeepestChildren('user-get-assos-members', 'client-get-assos-members'), // Pouvoir voir les assos membres
				\Scopes::matchOneOfDeepestChildren(
					['user-get-assos-followed', 'user-get-roles-assos'],
					['client-get-assos-followed', 'client-get-roles-assos']
				) // Pouvoir voir les assos que l'ont suit (donc pas de role) ou pouvoir voir les roles assos de l'utlisateur
			),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-get-assos', 'client-get-assos'), // Pouvoir voir les assos
				\Scopes::matchOneOfDeepestChildren('user-create-assos-members', 'client-create-assos-members'), // Pouvoir créé des assos membres
				\Scopes::matchOneOfDeepestChildren(
					['user-create-assos-followed', 'user-create-roles-assos'],
					['client-create-assos-followed', 'client-create-roles-assos']
				) // Pouvoir créer des assos que l'ont suit (donc pas de role) ou pouvoir créer des roles assos pour l'utlisateur
			),
			['only' => ['store']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-get-assos', 'client-get-assos'), // Pouvoir voir les assos
				\Scopes::matchOneOfDeepestChildren('user-edit-assos-members', 'client-edit-assos-members'), // Pouvoir modifier les assos membres
				\Scopes::matchOneOfDeepestChildren(
					['user-edit-assos-followed', 'user-edit-roles-assos'],
					['client-edit-assos-followed', 'client-edit-roles-assos']
				) // Pouvoir modifier les assos que l'ont suit (donc pas de role) ou pouvoir créer des roles assos pour l'utlisateur
			),
			['only' => ['update']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-get-assos', 'client-get-assos'), // Pouvoir voir les assos
				\Scopes::matchOneOfDeepestChildren('user-remove-assos-members', 'client-remove-assos-members'), // Pouvoir retirer les assos membres
				\Scopes::matchOneOfDeepestChildren(
					['user-remove-assos-followed', 'user-remove-roles-assos'],
					['client-remove-assos-followed', 'client-remove-roles-assos']
				) // Pouvoir retirer les assos que l'ont suit (donc pas de role) ou pouvoir retirer les roles assos de l'utlisateur
			),
			['only' => ['destroy']]
		);
	}

	/**
	 * On ajoute les droits admin en fonction du rôle qu'on a dans les assos
	 * @param Asso $asso
	 * @param Model $pivot
	 * @return null|string renvoie le type du role ajouté
	 */
	protected function addUserRoles($asso, $pivot): ?string {
		if (!is_null($pivot->validated_by)) {
			$adminAssos = config('portail.assos', []);

			if (isset($adminAssos[$asso->login])) {
				$adminRoles = $adminAssos[$asso->login];
				$role = Role::getRole($pivot->role_id);

				if (isset($adminRoles[$role->type])) {
					try {
						\Auth::user()->assignRoles($adminRoles[$role->type], [
							'validated_by' => \Auth::id(),
						], true);

						return $adminRoles[$role->type];
					} catch (\Exception $e) {
						// Dans le cas où on possède déjà ce rôle
					}
				}
			}
		}

		return null;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @param $asso_id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function index(Request $request, $asso_id): JsonResponse {
		$asso = $this->getAsso($request, $asso_id);
		$choices = $this->getChoices($request, ['members', 'joiners', 'followers']);
		$semester = Semester::getSemester($request->input('semester')) ?? Semester::getThisSemester();
		$members = collect();

		if ($request->input('semester') && !\Scopes::hasOne($request, ['user-get-assos-joined', 'user-get-assos-joining', 'user-get-assos-followed']))
			throw new PortailException('Il n\'est pas possible de définir un semestre particulier sans les scopes user-get-assos-joined ou user-get-assos-joining ou user-get-assos-followeds');

		if (\Scopes::has($request, 'user-get-assos-joined-now') && in_array('members', $choices))
			$members = $members->merge($asso->members()->where('semester_id', $semester->id)->get());

		if (\Scopes::has($request, 'user-get-assos-joining-now') && in_array('joiners', $choices))
			$members = $members->merge($asso->joiners()->where('semester_id', $semester->id)->get());

		if (\Scopes::has($request, 'user-get-assos-followed-now') && in_array('followers', $choices))
			$members = $members->merge($asso->followers()->where('semester_id', $semester->id)->get());

		$members = $members->map(function ($member) {
			return $member->hideData();
		});

		return response()->json($members, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  Request $request
	 * @param $asso_id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function store(Request $request, $asso_id): JsonResponse {
		$asso = $this->getAsso($request, $asso_id);

		if (\Scopes::isUserToken($request)) {
			if ($request->input('role_id')) {
				if (!\Scopes::hasOne($request, ['user-set-assos-joining-now', 'user-set-assos-joined-now']))
					abort(403, 'Vous n\'être pas autorisé à assigner un rôle');

				$asso->assignMembers(\Auth::id(), [
					'role_id' => $request->input('role_id'),
				]);
			}
			else {
				if (!\Scopes::hasOne($request, ['user-set-assos-joining-now', 'user-set-assos-joined-now']))
					abort(403, 'Vous n\'être pas autorisé à créer un membre sans rôle');

				$asso->assignMembers(\Auth::id(), [
					'validated_by' => \Auth::id(),
				]);
			}

			$member = $asso->allMembers()->wherePivot('user_id', \Auth::id())->first();
		}

		if ($new = $this->addUserRoles($asso, $member->pivot))
			$member->new_user_role = $new;

		return response()->json($member->hideData());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param $asso_id
	 * @param int $member_id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function show(Request $request, $asso_id, int $member_id): JsonResponse {
		$asso = $this->getAsso($request, $asso_id);
		$choices = $this->getChoices($request, ['members', 'joiners', 'followers']);
		$semester = Semester::getSemester($request->input('semester')) ?? Semester::getThisSemester();
		$member = null;

		if ($request->input('semester') && !\Scopes::hasOne($request, ['user-get-assos-joined', 'user-get-assos-joining', 'user-get-assos-followed']))
			throw new PortailException('Il n\'est pas possible de définir un semestre particulier sans les scopes user-get-assos-joined ou user-get-assos-joining ou user-get-assos-followeds');

		if (\Scopes::has($request, 'user-get-assos-joined-now') && in_array('members', $choices))
			$member = $asso->members()->where('semester_id', $semester->id)->wherePivot('user_id', $member_id)->first();

		if (is_null($member) && \Scopes::has($request, 'user-get-assos-joining-now') && in_array('joiners', $choices))
			$member = $asso->joiners()->where('semester_id', $semester->id)->wherePivot('user_id', $member_id)->first();

		if (is_null($member) && \Scopes::has($request, 'user-get-assos-followed-now') && in_array('followers', $choices))
			$member = $asso->followers()->where('semester_id', $semester->id)->wherePivot('user_id', $member_id)->first();

		if ($member)
			return response()->json($member->hideData());
		else
			abort(404, 'Cette personne ne fait pas partie de l\'association (ou vous ne pouvez pas le voir)');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param $asso_id
	 * @param int $member_id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function update(Request $request, $asso_id, int $member_id): JsonResponse {
		$asso = $this->getAsso($request, $asso_id);
		$choices = $this->getChoices($request, ['members', 'joiners', 'followers']);
		$semester = Semester::getSemester($request->input('semester')) ?? Semester::getThisSemester();
		$member = null;

		if ($request->input('semester') && !\Scopes::hasOne($request, ['user-get-assos-joined', 'user-get-assos-joining', 'user-get-assos-followed']))
			throw new PortailException('Il n\'est pas possible de définir un semestre particulier sans les scopes user-get-assos-joined ou user-get-assos-joining ou user-get-assos-followeds');

		if (\Scopes::has($request, 'user-get-assos-joined-now') && in_array('members', $choices))
			$member = $asso->members()->where('semester_id', $semester->id)->wherePivot('user_id', $member_id)->first();

		if (is_null($member) && \Scopes::has($request, 'user-get-assos-joining-now') && in_array('joiners', $choices))
			$member = $asso->joiners()->where('semester_id', $semester->id)->wherePivot('user_id', $member_id)->first();

		if (is_null($member) && \Scopes::has($request, 'user-get-assos-followed-now') && in_array('followers', $choices))
			$member = $asso->followers()->where('semester_id', $semester->id)->wherePivot('user_id', $member_id)->first();

		if ($member) {
			$role_id = $request->input('role_id', $member->pivot->role_id);

			if ($request->input('role_id') && !\Scopes::hasOne($request, ['user-set-assos-joining-now', 'user-set-assos-joined-now']))
				abort(403, 'Vous n\'être pas autorisé à modifier un rôle');
			else if (is_null($request->input('role_id')) && !\Scopes::hasOne($request, ['user-set-assos-joining-now', 'user-set-assos-joined-now']))
				abort(403, 'Vous n\'être pas autorisé à créer un membre sans rôle');

			// Si le rôle qu'on veut valider est un rôle qui peut-être validé par héridité
			$force = Role::getRole(config('portail.roles.admin.assos'))->id === $role_id && $asso->getLastUserWithRole(config('portail.roles.admin.assos'))->id === \Auth::id();

			$asso->updateMembers($member_id, [
				'semester_id' => $member->pivot->semester_id,
			], [
				                     'role_id'      => $role_id,
				                     'validated_by' => \Auth::id(),
			                     ], $force);

			$member = $asso->currentAllMembers()->where('user_id', $member_id)->first();

			if ($new = $this->addUserRoles($asso, $member->pivot))
				$member->new_user_role = $new;

			return response()->json($member->hideData());
		}
		else
			abort(404, 'Cette personne ne fait pas partie de l\'association');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param $asso_id
	 * @param int $member_id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function destroy(Request $request, $asso_id, int $member_id): JsonResponse {
		$choices = $this->getChoices($request, ['members', 'joiners', 'followers']);
		$semester = Semester::getSemester($request->input('semester')) ?? Semester::getThisSemester();
		$member = null;

		if ($request->input('semester') && !\Scopes::hasOne($request, ['user-get-assos-joined', 'user-get-assos-joining', 'user-get-assos-followed']))
			throw new PortailException('Il n\'est pas possible de définir un semestre particulier sans les scopes user-get-assos-joined ou user-get-assos-joining ou user-get-assos-followeds');

		if (\Scopes::has($request, 'user-get-assos-joined-now') && in_array('members', $choices))
			$member = $asso->members()->where('semester_id', $semester->id)->wherePivot('user_id', $member_id)->first();

		if (is_null($member) && \Scopes::has($request, 'user-get-assos-joining-now') && in_array('joiners', $choices))
			$member = $asso->joiners()->where('semester_id', $semester->id)->wherePivot('user_id', $member_id)->first();

		if (is_null($member) && \Scopes::has($request, 'user-get-assos-followed-now') && in_array('followers', $choices))
			$member = $asso->followers()->where('semester_id', $semester->id)->wherePivot('user_id', $member_id)->first();

		if ($member) {
			$asso->removeMembers($member_id, [
				'semester_id' => $semester->id,
			], \Auth::id());

			abort(203);
		}
		else
			abort(404, 'Cette personne ne faisait déjà pas partie de l\'association (ou vous ne pouvez pas le voir)');
	}
}
