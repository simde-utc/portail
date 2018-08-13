<?php

namespace App\Traits\Controller\v1;

use App\Models\Asso;
use App\Models\User;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Traits\Controller\v1\HasUsers;

trait HasAssos
{
	use HasUsers;

	protected function getSemester(Request $request, array $choices, string $verb = 'get') {
		$scopeHead = \Scopes::getTokenType($request);

		if ($request->filled('semester')) {
			if (in_array('joined', $choices) && !\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-assos-members-joined-now'))
				throw new PortailException('Vous n\'avez pas les droits pour spécifier un semestre particulier pour les associations rejoins par l\'utilisateur');

			if (in_array('joining', $choices) && !\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-assos-members-joining-now'))
				throw new PortailException('Vous n\'avez pas les droits pour spécifier un semestre particulier pour les associations que l\'utilisateur a demandé à rejoindre');

			if (in_array('followed', $choices) && !\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-assos-members-followed-now'))
				throw new PortailException('Vous n\'avez pas les droits pour spécifier un semestre particulier pour les associations que l\'utilisateur suit');

			return Semester::getSemester($request->input('semester'));
		}

		return Semester::getThisSemester();
	}

	protected function getChoices(Request $request, array $initialChoices = ['joined', 'joining', 'followed']) {
		$scopeHead = \Scopes::getTokenType($request);
		$choices = [];

		foreach ($initialChoices as $choice) {
			if (\Scopes::hasOne($request, $scopeHead.'-get-assos-members-'.$choice.'-now'))
				$choices[] = $choice;
		}

		return parent::getChoices($request, $choices);
	}

	/**
	 * Récupère une association par son id si elle existe
	 * @param Request $request
	 * @param $asso_id
	 * @return Asso
	 */
	protected function getAsso(Request $request, $asso_id, User $user = null, Semester $semester = null): Asso {
		if (is_numeric($asso_id))
			$asso = Asso::find($asso_id);
		else
			$asso = Asso::findByLogin($asso_id);

		if ($asso) {
			if ($user) {
				$asso = $user->assos()
					->wherePivot('semester_id', $semester ? $semester->id : Semester::getThisSemester())
					->wherePivot('asso_id', $asso->id)
					->first();

				if (!$asso)
					abort(403, 'L\'utilisateur ne fait pas parti de l\'association');
			}

			return $asso;
		}
		else
			abort(404, "Assocation non trouvée");
	}

	protected function getUserFromAsso(Request $request, Asso $asso, string $user_id, Semester $semester = null) {
		$user = $asso->allMembers()
			->wherePivot('user_id', $this->getUser($request, $user_id, true)->id)
			->wherePivot('semester_id', $semester ? $semester->id : Semester::getThisSemester())
			->first();

		if ($user)
			return $user;
		else
			abort(403, 'L\'utilisateur ne fait pas parti de l\'association');
	}
}
