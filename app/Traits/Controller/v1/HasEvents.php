<?php
/**
 * Ajoute au controlleur un accès aux événements.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\Event;
use App\Models\User;
use App\Models\Asso;
use App\Models\Calendar;
use App\Models\Client;
use App\Traits\HasVisibility;
use Illuminate\Http\Request;
use App\Models\Model;

trait HasEvents
{
    use HasVisibility, HasUsers;

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

        return $model->owned_by->isEventAccessibleBy($user_id);
    }

    /**
     * Uniquement les followers et ceux qui possèdent le droit peuvent le voir.
     *
     * @param  Request $request
     * @param  Event   $event
     * @param  string  $user_id
     * @return boolean
     */
    protected function isEventFollowed(Request $request, Event $event, string $user_id)
    {
        $user = User::find($user_id);
        $calendar_ids = $user->calendars()->get(['calendars.id'])->pluck('id')->merge(
            $user->followedCalendars()->get(['calendars.id'])->pluck('id')
        );
        $event_calendar_ids = $event->calendars()->get(['calendars.id'])->pluck('id');

        $type = \ModelResolver::getName($event->owned_by_type);

        return (
            count($calendar_ids->intersect($event_calendar_ids)) !== 0
            && \Scopes::hasOne($request, \Scopes::getTokenType($request).'-get-events-users-followed-'.$type)
        );
    }

    /**
     * Récupère l'événement.
     *
     * @param  Request $request
     * @param  User    $user
     * @param  string  $event_id
     * @param  string  $verb
     * @return Event|null
     */
    protected function getEvent(Request $request, User $user=null, string $event_id, string $verb='get')
    {
        $event = Event::find($event_id);

        if ($event) {
            // On vérifie si l'accès est publique.
            if (\Scopes::isOauthRequest($request)) {
                if (!$this->tokenCanSee($request, $event, $verb, 'events')) {
                    abort(403, 'L\'application n\'a pas les droits sur cet évènenement');
                }

                if ($user && !$this->isVisible($event, $user->id) && !$this->isEventFollowed($request, $event, $user->id)) {
                    abort(403, 'Vous n\'avez pas les droits sur cet évènenement');
                }

                if ($verb !== 'get' && !$event->owned_by->isEventManageableBy(\Auth::id())) {
                    abort(403, 'Vous n\'avez pas les droits suffisants');
                }
            } else {
                if (!$this->isVisible($event)) {
                    abort(403, 'Vous n\'avez pas les droits sur cet événement');
                }
            }

            return $event;
        }

        abort(404, 'Impossible de trouver le évènenement');
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
     * Retourne le calendrier.
     *
     * @param  Request $request
     * @param  User    $user
     * @param  string  $calendar_id
     * @param  string  $verb
     * @return Calendar|null
     */
    protected function getCalendar(Request $request, User $user=null, string $calendar_id, string $verb='get')
    {
        $calendar = Calendar::find($calendar_id);

        if ($calendar) {
            if (!$this->isCalendarFollowed($request, $calendar, $user->id)) {
                if (!$this->tokenCanSee($request, $calendar, $verb)) {
                    abort(403, 'L\'application n\'a pas les droits sur ce calendrier');
                }

                if ($user && !$this->isVisible($calendar, $user->id)) {
                    abort(403, 'Vous n\'avez pas les droits sur ce calendrier');
                }
            }

            if ($verb !== 'get' && \Scopes::isUserToken($request) && !$calendar->owned_by->isCalendarManageableBy($user->id)) {
                abort(403, 'Vous n\'avez pas les droits suffisants');
            }

            return $calendar;
        }

        abort(404, 'Impossible de trouver le calendrier');
    }

    /**
     * Indique si le token peut voir ou non.
     *
     * @param  Request $request
     * @param  Model   $model
     * @param  string  $verb
     * @param  string  $type
     * @return boolean
     */
    protected function tokenCanSee(Request $request, Model $model, string $verb, string $type='events')
    {
        $scopeHead = \Scopes::getTokenType($request);
        $name = \ModelResolver::getName($model->owned_by_type);

        if (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$name.'s-owned')) {
            return true;
        }

        if (((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$name.'s-owned-user'))
                && \Auth::id()
                && $model->created_by_type === User::class
                && $model->created_by_id === \Auth::id()
            ) || (
                (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$name.'s-owned-client'))
                && $model->created_by_type === Client::class
                && $model->created_by_id === \Scopes::getClient($request)->id
            ) || (
                (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$name.'s-owned-asso'))
                && $model->created_by_type === Asso::class
                && $model->created_by_id === \Scopes::getClient($request)->asso->id)
            ) {
            if (\Scopes::isUserToken($request)) {
                $functionToCall = 'is'.($type === 'calendars' ? 'Calendar' : 'Event');
                $functionToCall .= ($verb === 'get' ? 'Accessible' : 'Manageable').'By';

                if ($model->owned_by->$functionToCall(\Auth::id())) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return \Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$name.'s-created');
    }
}
