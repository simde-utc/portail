<?php
/**
 * Model corresponding to user details.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use App\Traits\Model\HasKeyValue;
use App\Models\User;
use App\Exceptions\PortailException;
use Illuminate\Database\Eloquent\Builder;

class UserDetail extends Model
{
    use HasKeyValue;

    protected $table = 'users_details';

    protected $fillable = [
        'user_id', 'key', 'value', 'type',
    ];

    protected $must = [
        'key', 'value'
    ];

    protected $functionalKeys = [
        'age', 'isMajor', 'loginCAS', 'loginContributorBde', 'isContributorBde',
    ];

    /**
     * Retrieve the user on wich we're working on from a query.
     *
     * @param Builder $query
     * @return User|null
     */
    protected function getUserFromQuery(Builder $query)
    {
        $wheres = $query->getQuery()->wheres;

        foreach ($wheres as $where) {
            if ($where['column'] === $this->getTable().'.user_id' && $where['operator'] === '=') {
                return User::find($where['value']);
            }
        }

        return null;
    }

    /**
     * Give the user age.
     *
     * @param  Builder $query
     * @return integer
     */
    public function age(Builder $query)
    {
        $birthdate = $query->valueOf('birthdate');

        if ($birthdate) {
            return $birthdate->age;
        } else {
            throw new PortailException('Non trouvé');
        }
    }

    /**
     * Indicate if a user is major.
     *
     * @param  Builder $query
     * @return boolean
     */
    public function isMajor(Builder $query)
    {
        $casLogin = $this->loginCAS($query);

        if ($casLogin) {
            try {
                return \Ginger::user($casLogin)->isAdult();
            } catch (\Exception $e) {
                // If we don't have CAS data, we check the birthdate.
            }
        }

        $age = $this->age($query);

        if ($age) {
            return $age >= 18;
        } else {
            throw new PortailException('Non trouvé');
        }
    }

    /**
     * Return the user CAS login.
     *
     * @param  Builder $query
     * @return string
     */
    public function loginCAS(Builder $query)
    {
        try {
            $cas = $this->getUserFromQuery($query)->cas;
        } catch (\ErrorException $e) {
            $cas = null;
        }

        if ($cas) {
            return $cas->login;
        } else {
            throw new PortailException('Non trouvé');
        }
    }

    /**
     * Return the BDE-UTC contributor login.
     *
     * @param  Builder $query
     * @return string
     */
    public function loginContributorBde(Builder $query)
    {
        try {
            $casLogin = $this->loginCAS($query);
        } catch (PortailException $e) {
            $casLogin = null;
        }

        if ($casLogin) {
            return $casLogin;
        } else {
            try {
                return \Ginger::userByEmail($this->getUserFromQuery($query)->email)->getLogin();
            } catch (\ErrorException $e) {
                throw new PortailException('Non trouvé');
            }
        }
    }

    /**
     * Indicate if a given user is BDE-UTC contributor.
     *
     * @param  Builder $query
     * @return boolean
     */
    public function isContributorBde(Builder $query)
    {
        $login = $this->loginContributorBde($query);

        if ($login) {
            try {
                if (!is_null($response = \Ginger::user($login)->isContributor())) {
                    return $response;
                }
            } catch (\Exception $e) {
                // In the case of the user is not a BDE-UTC contributor, we send a custom exception.
            }
        }

        throw new PortailException('Non trouvé');
    }
}
