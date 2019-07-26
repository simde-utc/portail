<?php
/**
 * Add the controller an access to Events.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use Carbon\Carbon;
use App\Exceptions\PortailException;
use App\Models\Event;
use App\Models\User;
use App\Models\Asso;
use App\Models\Calendar;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\Model;

trait HasEvents
{
    use HasUsers;

    /**
     * Check that there is no problem with atime period.
     *
     * @param  string $begin_at
     * @param  string $end_at
     * @return void
     */
    protected function checkPeriod(string $begin_at, string $end_at)
    {
        $begin = Carbon::parse($begin_at);
        $end = Carbon::parse($end_at);

        if ($begin->lessThanOrEqualTo(Carbon::now())) {
            abort(400, 'La date de début d\'événement doit être postérieure à la date actuelle');
        }

        if ($begin->addMinutes(5)->greaterThan($end)) {
            abort(400, 'L\'événement doit avoir une durée d\'au moins 5 min');
        }
    }

    /**
     * Retrieve the event.
     *
     * @param  Request $request
     * @param  User    $user
     * @param  string  $event_id
     * @param  string  $verb
     * @return Event|null
     */
    protected function getEvent(Request $request, User $user=null, string $event_id, string $verb='get')
    {
        Event::setUserForVisibility($user);
        $event = Event::findSelection($event_id);

        if ($event) {
            // Public access checking.
            if (\Scopes::isOauthRequest($request)) {
                if (!$this->tokenCanSee($request, $event, $verb, 'events')) {
                    abort(403, 'L\'application n\'a pas les droits sur cet évènenement');
                }

                if ($verb !== 'get' && \Scopes::isUserToken($request)
                    && !$event->owned_by->isEventManageableBy(\Auth::id())) {
                    abort(403, 'Vous n\'avez pas les droits suffisants');
                }
            }

            return $event;
        }

        abort(404, 'Impossible de trouver l\'évènenement');
    }

    /**
     * Return a given calendar.
     *
     * @param  Request $request
     * @param  User    $user
     * @param  string  $calendar_id
     * @param  string  $verb
     * @return Calendar|null
     */
    protected function getCalendar(Request $request, User $user=null, string $calendar_id, string $verb='get')
    {
        Calendar::setUserForVisibility($user);
        $calendar = Calendar::findSelection($calendar_id);

        if ($calendar) {
            // Public access checking.
            if (\Scopes::isOauthRequest($request)) {
                if (!$this->tokenCanSee($request, $calendar, $verb, 'calendars')) {
                    abort(403, 'L\'application n\'a pas les droits sur ce calendrier');
                }

                if ($verb !== 'get' && \Scopes::isUserToken($request)
                    && !$calendar->owned_by->isCalendarManageableBy($user->id)) {
                    abort(403, 'Vous n\'avez pas les droits suffisants');
                }
            }

            return $calendar;
        }

        abort(404, 'Impossible de trouver le calendrier');
    }

    /**
     * Indicate if the token can see or not.
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

        if ($name !== 'user' || $model->owned_by_id !== \Auth::id()) {
            return \Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-users-followed-'.$name);
        }

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
            return true;
        }

        return \Scopes::hasOne($request, $scopeHead.'-'.$verb.'-'.$type.'-'.$name.'s-created');
    }
}
