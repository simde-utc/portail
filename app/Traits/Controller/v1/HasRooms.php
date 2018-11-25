<?php
/**
 * Ajoute au controlleur un accès aux salles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Traits\HasVisibility;
use App\Models\Asso;
use App\Models\Room;
use App\Models\Model;
use App\Models\User;
use Illuminate\Http\Request;

trait HasRooms
{
    use HasVisibility;

    /**
     * Indique que l'utilisateur est membre de l'instance.
     *
     * @param  string $user_id
     * @param  mixed  $room
     * @return boolean
     */
    public function isPrivate(string $user_id, $room=null)
    {
        if ($room === null) {
            return false;
        }

        return $room->owned_by->isRoomAccessibleBy($user_id);
    }

    /**
     * Récupère une salle.
     *
     * @param  Request $request
     * @param  User    $user
     * @param  string  $room_id
     * @param  string  $verb
     * @return Room|null
     */
    protected function getRoom(Request $request, User $user, string $room_id, string $verb='get')
    {
        $room = Room::find($room_id);

        if ($room) {
            if (!$this->tokenCanSee($request, $room, $verb)) {
                abort(403, 'L\'application n\'a pas les droits sur cet salle');
            }

            if (!$this->isVisible($room, $user->id)) {
                abort(403, 'Vous n\'avez pas le droit de voir cette salle');
            }

            if ($verb !== 'get' && !$room->owned_by->isRoomManageableBy(\Auth::id())) {
                abort(403, 'Vous n\'avez pas les droits suffisants');
            }

            return $room;
        }

        abort(404, 'Impossible de trouver la salle');
    }

    /**
     * Indique si le token peut voir la ressource.
     *
     * @param  Request $request
     * @param  Room    $room
     * @param  string  $verb
     * @return boolean
     */
    protected function tokenCanSee(Request $request, Room $room, string $verb)
    {
        $scopeHead = \Scopes::getTokenType($request);
        $type = \ModelResolver::getName($room->owned_by_type);

        if (\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-rooms-'.$type.'s-owned')) {
            return true;
        }

        if (((\Scopes::hasOne($request, $scopeHead.'-'.$verb.'-rooms-'.$type.'s-owned-asso'))
            && $room->created_by_type === Asso::class
            && $room->created_by_id === \Scopes::getClient($request)->asso->id)) {
            if (\Scopes::isUserToken($request)) {
                $functionToCall = 'isRoom'.($verb === 'get' ? 'Accessible' : 'Manageable').'By';

                if ($room->owned_by->$functionToCall(\Auth::id())) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return \Scopes::hasOne($request, $scopeHead.'-'.$verb.'-rooms-'.$type.'s-created');
    }
}
