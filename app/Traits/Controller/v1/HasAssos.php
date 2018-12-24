<?php
/**
 * Ajoute au controlleur un accès aux associations.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Models\Asso;
use App\Models\User;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Traits\Controller\v1\HasUsers;
use App\Traits\Controller\v1\HasSemesters;
use App\Exceptions\PortailException;

trait HasAssos
{
    use HasUsers, HasSemesters {
        getSemester as protected getSemesterFromTrait;
    }

    /**
     * Récupère le semestre en fonction des choix.
     *
     * @param  Request $request
     * @param  array   $choices
     * @param  string  $verb
     * @return Semester
     */
    protected function getSemester(Request $request, array $choices, string $verb='get')
    {
        $scopeHead = \Scopes::getTokenType($request);

        if ($request->filled('semester')) {
            if (in_array('joined', $choices)
                && !\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-assos-members-joined-now')) {
                throw new PortailException('Vous n\'avez pas les droits pour spécifier un semestre particulier pour \
                    les associations rejoins par l\'utilisateur');
            }

            if (in_array('joining', $choices)
                && !\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-assos-members-joining-now')) {
                throw new PortailException('Vous n\'avez pas les droits pour spécifier un semestre particulier pour \
                    les associations que l\'utilisateur a demandé à rejoindre');
            }

            if (in_array('followed', $choices)
                && !\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-assos-members-followed-now')) {
                throw new PortailException('Vous n\'avez pas les droits pour spécifier un semestre particulier pour \
                    les associations que l\'utilisateur suit');
            }

            return $this->getSemesterFromTrait($request->input('semester'));
        }

        return Semester::getThisSemester();
    }

    /**
     * Récupère la liste de choix.
     *
     * @param  Request $request
     * @param  array   $initialChoices
     * @return array
     */
    protected function getChoices(Request $request, array $initialChoices=['joined', 'joining'])
    {
        $scopeHead = \Scopes::getTokenType($request);
        $choices = [];

        foreach ($initialChoices as $choice) {
            if (\Scopes::hasOne($request, $scopeHead.'-get-assos-members-'.$choice.'-now')) {
                $choices[] = $choice;
            }
        }

        return parent::getChoices($request, $choices);
    }

    /**
     * Récupère une association par son id si elle existe.
     *
     * @param Request  $request
     * @param string   $asso_id
     * @param User     $user
     * @param Semester $semester
     * @return Asso
     */
    protected function getAsso(Request $request, string $asso_id, User $user=null, Semester $semester=null): Asso
    {
        if (\Uuid::validate($asso_id)) {
            $asso = Asso::with('parent')->find($asso_id);
        } else {
            $asso = Asso::with('parent')->findByLogin($asso_id);
        }

        if ($asso) {
            if ($user) {
                $asso = $user->assos()
                    ->wherePivot('semester_id', $semester ? $semester->id : Semester::getThisSemester())
                    ->wherePivot('asso_id', $asso->id)
                    ->first();

                if (!$asso) {
                    abort(404, 'L\'utilisateur ne fait pas parti de l\'association');
                }
            }

            return $asso;
        } else {
            abort(404, "Assocation non trouvée");
        }
    }

    /**
     * Récupère un utilisateur à partir d'une association.
     *
     * @param  Request  $request
     * @param  Asso     $asso
     * @param  string   $user_id
     * @param  Semester $semester
     * @return mixed
     */
    protected function getUserFromAsso(Request $request, Asso $asso, string $user_id, Semester $semester=null)
    {
        $user = $asso->allMembers()
            ->wherePivot('user_id', $this->getUser($request, $user_id, true)->id)
            ->wherePivot('semester_id', $semester ? $semester->id : Semester::getThisSemester())
            ->whereNotNull('role_id')
            ->first();

        if ($user) {
            return $user;
        } else {
            abort(404, 'L\'utilisateur ne fait pas parti de l\'association');
        }
    }

    /**
     * Récupère une association depuis un utilisateur.
     *
     * @param  Request  $request
     * @param  string   $asso_id
     * @param  string   $user_id
     * @param  Semester $semester
     * @return mixed
     */
    protected function getAssoFromMember(Request $request, string $asso_id, string $user_id=null, Semester $semester=null)
    {
        if ($user_id) {
            $asso = User::find($user_id)->joinedAssos()
                ->wherePivot('semester_id', $semester ? $semester->id : Semester::getThisSemester())
                ->wherePivot('asso_id', $asso_id)
                ->first();

            if ($asso) {
                return $asso;
            } else {
                abort(404, 'L\'utilisateur ne fait pas parti de l\'association');
            }
        } else {
            return $this->getAsso($request, $asso_id);
        }
    }
}
