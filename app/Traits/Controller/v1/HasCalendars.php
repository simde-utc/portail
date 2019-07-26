<?php
/**
 * Add the controller an access to Calendars.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\{
    User, Calendar, Event, Model
};
use Illuminate\Http\Request;

trait HasCalendars
{
    use HasEvents {
        HasEvents::tokenCanSee as tokenCanSeeEvent;
    }

    /**
     * Retrieve an event from the calendar.
     *
     * @param  Request  $request
     * @param  User     $user
     * @param  Calendar $calendar
     * @param  string   $event_id
     * @param  string   $verb
     * @return Event|null
     */
    protected function getEventFromCalendar(Request $request, User $user=null, Calendar $calendar, string $event_id,
        string $verb='get')
    {
        $event = $calendar->events()->findSelection($event_id);

        if ($event) {
            if (\Scopes::isOauthRequest($request)) {
                if (!$this->tokenCanSee($request, $event, $verb, 'events')) {
                    abort(403, 'L\'application n\'a pas les droits sur cet évènenement');
                }
            }

            return $event;
        }

        abort(404, 'L\'événement n\'existe pas ou ne fait pas parti du calendrier');
    }

    /**
     * Return if the token can see the ressource or not.
     *
     * @param  Request $request
     * @param  Model   $model
     * @param  string  $verb
     * @param  string  $type
     * @return boolean
     */
    protected function tokenCanSee(Request $request, Model $model, string $verb, string $type='calendars')
    {
        return $this->tokenCanSeeEvent($request, $model, $verb, $type);
    }
}
