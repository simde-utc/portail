<?php
/**
 * Ajoute au controlleur un accès aux calendriers.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\User;
use App\Models\Calendar;
use App\Models\Event;
use App\Facades\Ginger;
use App\Models\Model;
use Illuminate\Http\Request;

trait HasCalendars
{
    use HasEvents {
        HasEvents::isPrivate as isEventPrivate;
        HasEvents::tokenCanSee as tokenCanSeeEvent;
    }

    /**
     * Indique que l'utilisateur est membre de l'instance.
     *
     * @param  string $user_id
     * @param  mixed  $model
     * @return boolean
     */
    public function isPrivate(string $user_id, $model=null)
    {
        if ($model === null) {
            return false;
        }

        if ($model instanceof Event) {
            return $this->isEventPrivate($user_id, $model);
        }

        return $model->owned_by->isCalendarAccessibleBy($user_id);
    }

    /**
     * Uniquement les followers et ceux qui possèdent le droit peuvent le voir.
     *
     * @param  Request  $request
     * @param  Calendar $calendar
     * @param  string   $user_id
     * @return boolean
     */
    protected function isCalendarFollowed(Request $request, Calendar $calendar, string $user_id)
    {
        $type = \ModelResolver::getName($calendar->owned_by_type);

        return (
            $calendar->followers()->wherePivot('user_id', $user_id)->exists()
            && \Scopes::hasOne($request, \Scopes::getTokenType($request).'-get-calendars-users-followed-'.$type)
        );
    }

    /**
     * Récupère un événement depuis le calendrier.
     *
     * @param  Request  $request
     * @param  User     $user
     * @param  Calendar $calendar
     * @param  string   $event_id
     * @param  string   $verb
     * @return Event|null
     */
    protected function getEventFromCalendar(Request $request, User $user, Calendar $calendar, string $event_id,
        string $verb='get')
    {
        $event = $calendar->events()->find($event_id);

        if ($event) {
            if (!$this->tokenCanSee($request, $event, $verb, 'events')) {
                abort(403, 'L\'application n\'a pas les droits sur cet évènenement');
            }

            if ($user && !$this->isVisible($event, $user->id)) {
                abort(403, 'Vous n\'avez pas les droits sur cet évènenement');
            }

            return $event;
        }

        abort(404, 'L\'événement n\'existe pas ou ne fait pas parti du calendrier');
    }

    /**
     * Indique si le token peut voir la ressource.
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
