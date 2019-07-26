<?php
/**
 * Add to the controller an access to Services.
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

trait HasServices
{
    use HasUsers;

    /**
     * Retrieve a Service.
     *
     * @param  User   $user
     * @param  string $service_id
     * @return Service|null
     */
    protected function getService(User $user=null, string $service_id)
    {
        $service = Service::setUserForVisibility($user)::findSelection($service_id);

        if ($service) {
            return $service;
        }

        abort(404, 'Impossible de trouver le service');
    }

    /**
     * Retrieve a followed service.
     *
     * @param  User   $user
     * @param  string $service_id
     * @return Service|null
     */
    protected function getFollowedService(User $user=null, string $service_id)
    {
        Service::setUserForVisibility($user);
        $service = $user->followedServices()->findSelection($service_id);

        if ($service) {
            return $service;
        }

        abort(404, 'Impossible de trouver le service ou il n\'est pas suivi par l\'utilisateur');
    }
}
