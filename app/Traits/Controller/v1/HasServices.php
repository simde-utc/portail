<?php
/**
 * Ajoute au controlleur un accès aux services.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\User;
use App\Models\Service;
use App\Models\Model;
use Illuminate\Http\Request;
use App\Traits\HasVisibility;

trait HasServices
{
    use HasUsers, HasVisibility;

    /**
     * Indique que l'utilisateur est membre de l'instance.
     *
     * @param  string $user_id
     * @param  mixed  $model
     * @return boolean
     */
    protected function isPrivate(string $user_id=null, $model=null)
    {
        return User::find($user_id)->permissions()->count() > 0;
    }

    /**
     * Récupère un service.
     *
     * @param  User   $user
     * @param  string $service_id
     * @return Service|null
     */
    protected function getService(User $user=null, string $service_id)
    {
        $service = Service::find($service_id);

        if ($service) {
            if (!$this->isVisible($service, $user->id)) {
                abort(403, 'Vous n\'avez pas le droit de voir ce service');
            }

            return $service;
        }

        abort(404, 'Impossible de trouver le service');
    }

    /**
     * Récupère un service suivi.
     *
     * @param  User   $user
     * @param  string $service_id
     * @return Service|null
     */
    protected function getFollowedService(User $user=null, string $service_id)
    {
        $service = $user->followedServices()->find($service_id);

        if ($service) {
            return $service;
        }

        abort(404, 'Impossible de trouver le service ou il n\'est pas suivi par l\'utilisateur');
    }
}
