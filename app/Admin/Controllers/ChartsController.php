<?php
/**
 * Display charts.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers;

use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use App\Models\{
    User, AuthCas, AuthPassword, AuthApp, Semester
};

class ChartsController extends Controller
{
    /**
     * Give access only if the user has the right permission.
     */
    public function __construct()
    {
        $this->middleware('permission:admin');
    }

    /**
     * Displays a lot of charts.
     *
     * @param  Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Charts')
            ->body(new Box('Utilisateurs', view('admin.charts.users', $this->getUserData())))
            ->body(new Box('Membres des associations', view('admin.charts.assos_members', $this->getMemberData())));
    }

    /**
     * Retrieve user data.
     *
     * @return array
     */
    protected function getUserData(): array
    {
        $users = [];
        $usersBar = [];
        $nbr = 0;

        foreach (User::orderBy('created_at')->get(['created_at']) as $user) {
            $date = $user->created_at->format('d/m/Y');
            $users[$date] = ++$nbr;

            if (isset($usersBar[$date])) {
                $usersBar[$date]++;
            } else {
                $usersBar[$date] = 1;
            }
        }

        return [
            'users' => $users,
            'usersBar' => $usersBar,
            'auths' => [
                'CAS-UTC' => AuthCas::count(),
                'Email/Mot de passe' => AuthPassword::count(),
                'Application' => AuthApp::count()
            ]
        ];
    }

    /**
     * Retrieve all member users.
     *
     * @return array
     */
    protected function getMemberData()
    {
        $semesters = Semester::get();
        $memberList = DB::select('SELECT created_at FROM assos_members WHERE role_id is not NULL ORDER BY created_at');
        $uniqueMemberList = DB::select('SELECT count(asso_id) as assos, created_at FROM assos_members WHERE role_id is not
            NULL GROUP BY user_id, semester_id ORDER BY created_at');
        $members = [];
        $membersByDay = [];
        $membersByMonth = [];
        $membersBySemester = [];
        $nbr = 0;

        foreach ($memberList as $member) {
            $date = (new Carbon($member->created_at))->format('d/m/Y');
            $members[$date] = ++$nbr;
            $membersByDay[$date] = (($membersByDay[$date] ?? 0) + 1);

            $date = (new Carbon($member->created_at))->format('m/Y');
            $membersByMonth[$date] = (($membersByMonth[$date] ?? 0) + 1);

            foreach ($semesters as $semester) {
                if ($member->created_at >= $semester->begin_at && $member->created_at <= $semester->end_at) {
                    $membersBySemester[$semester->name] = (($membersBySemester[$semester->name] ?? 0) + 1);
                    break;
                }
            }
        }

        $uMembers = [];
        $uMembersByDay = [];
        $uMembersByMonth = [];
        $uMembersBySemester = [];
        $assosPerMemberKeys = [];
        $assosPerMemberValues = [];
        $nbr = 0;

        foreach ($uniqueMemberList as $member) {
            $date = (new Carbon($member->created_at))->format('d/m/Y');
            $uMembers[$date] = ++$nbr;
            $uMembersByDay[$date] = (($uMembersByDay[$date] ?? 0) + 1);

            $date = (new Carbon($member->created_at))->format('m/Y');
            $uMembersByMonth[$date] = (($uMembersByMonth[$date] ?? 0) + 1);

            foreach ($semesters as $semester) {
                if ($member->created_at >= $semester->begin_at && $member->created_at <= $semester->end_at) {
                    $uMembersBySemester[$semester->name] = (($uMembersBySemester[$semester->name] ?? 0) + 1);

                    if (isset($assosPerMemberValues[$semester->name])) {
                        $assosPerMemberValues[$semester->name][$member->assos] = (
                            ($assosPerMemberValues[$semester->name][$member->assos] ?? 0) + 1
                        );
                    } else {
                        $assosPerMemberValues[$semester->name] = [$member->assos => 1];
                    }

                    break;
                }
            }

            $assosPerMemberKeys[$member->assos] = true;
        }

        foreach ($assosPerMemberValues as $semesterName => $assosPerMemberValue) {
            $assosPerMemberValues[$semesterName] = $this->adjustKeysWith0($assosPerMemberKeys, $assosPerMemberValue);
        }

        $uMembers = $this->adjustKeys($members, $uMembers);
        $uMembersByDay = $this->adjustKeys($membersByDay, $uMembersByDay);
        $uMembersByMonth = $this->adjustKeys($membersByMonth, $uMembersByMonth);
        $uMembersBySemester = $this->adjustKeys($membersBySemester, $uMembersBySemester);

        return compact('members', 'membersByDay', 'membersByMonth', 'membersBySemester', 'uMembers',
            'uMembersByDay', 'uMembersByMonth', 'uMembersBySemester', 'assosPerMemberKeys', 'assosPerMemberValues');
    }

    /**
     * Adjust arrays to have the same keys.
     *
     * @param  array $reference
     * @param  array $data
     * @return array
     */
    protected function adjustKeys(array $reference, array $data)
    {
        $lastValue = 0;
        $newData = [];

        foreach (array_keys($reference) as $key) {
            $lastValue = ($newData[$key] = ($data[$key] ?? $lastValue));
        }

        return $newData;
    }

    /**
     * Adjust arrays to have same keys by assining 0.
     *
     * @param  array $reference
     * @param  array $data
     * @return array
     */
    protected function adjustKeysWith0(array $reference, array $data)
    {
        $newData = [];

        foreach (array_keys($reference) as $key) {
            $newData[$key] = ($data[$key] ?? 0);
        }

        return $newData;
    }
}
