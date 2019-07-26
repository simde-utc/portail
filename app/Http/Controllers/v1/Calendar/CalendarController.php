<?php
/**
 * Manage the calendars.
 *
 * TODO: En abort
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natan.danous@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Calendar;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasCalendars;
use App\Traits\Controller\v1\HasCreatorsAndOwners;
use App\Http\Requests\CalendarRequest;
use App\Models\User;
use App\Models\Asso;
use App\Models\Calendar;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Interfaces\Model\CanHaveCalendars;

class CalendarController extends Controller
{
    use HasCalendars, HasCreatorsAndOwners;

    /**
     * Must be able to manage calendars.
     */
    public function __construct()
    {
        $this->middleware(
	        \Scopes::allowPublic()->matchOneOfDeepestChildren('user-get-calendars', 'client-get-calendars'),
	        ['only' => ['all', 'get']]
        );
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren('user-create-calendars', 'client-create-calendars'),
	        ['only' => ['create']]
        );
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren('user-edit-calendars', 'client-edit-calendars'),
	        ['only' => ['edit']]
        );
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren('user-manage-calendars', 'client-manage-calendars'),
	        ['only' => ['remove']]
        );
    }

    /**
     * List the calendars.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $calendars = Calendar::getSelection();

        if (\Scopes::isOauthRequest($request)) {
            $calendars = $calendars->filter(function ($calendar) use ($request) {
                return $this->tokenCanSee($request, $calendar, 'get');
            });
        }

        return response()->json($calendars->values()->map(function ($calendar) {
            return $calendar->hideData();
        }), 200);
    }

    /**
     * Create a calendar.
     *
     * @param CalendarRequest $request
     * @return JsonResponse
     */
    public function store(CalendarRequest $request): JsonResponse
    {
        $inputs = $request->all();

        $owner = $this->getOwner($request, 'calendar', 'calendrier', 'create');
        $creator = $this->getCreatorFromOwner($request, $owner, 'calendar', 'calendrier', 'create');

        $inputs['created_by_id'] = $creator->id;
        $inputs['created_by_type'] = get_class($creator);
        $inputs['owned_by_id'] = $owner->id;
        $inputs['owned_by_type'] = get_class($owner);

        $calendar = Calendar::create($inputs);

        $calendar = $this->getCalendar($request, \Auth::user(), $calendar->id);

        return response()->json($calendar->hideSubData(), 201);
    }

    /**
     * Show a calendar.
     *
     * @param Request	$request
     * @param string 	$calendrier_id
     * @return JsonResponse
     */
    public function show(Request $request, string $calendrier_id): JsonResponse
    {
        $calendar = $this->getCalendar($request, \Auth::user(), $calendrier_id);

        return response()->json($calendar->hideSubData(), 200);
    }

    /**
     * Update a calendar.
     *
     * @param CalendarRequest	$request
     * @param string          $calendrier_id
     * @return JsonResponse
     */
    public function update(CalendarRequest $request, string $calendrier_id): JsonResponse
    {
        $calendar = $this->getCalendar($request, \Auth::user(), $calendrier_id, 'edit');
        $inputs = $request->all();

        if ($request->filled('owned_by_type')) {
            $owner = $this->getOwner($request, 'calendar', 'calendrier', 'edit');

            $inputs['owned_by_id'] = $owner->id;
            $inputs['owned_by_type'] = get_class($owner);
        }

        if ($calendar->update($inputs)) {
            return response()->json($calendar->hideSubData(), 200);
        } else {
            abort(500, 'Impossible de modifier le calendrier');
        }
    }

    /**
     * Delete a calendar.
     *
     * @param Request	$request
     * @param string 	$calendrier_id
     * @return void
     */
    public function destroy(Request $request, string $calendrier_id): void
    {
        $calendar = $this->getCalendar($request, \Auth::user(), $calendrier_id, 'manage');
        $calendar->delete();

        abort(204);
    }
}
