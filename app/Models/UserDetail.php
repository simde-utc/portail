<?php
/**
 * Modèle correspondant aux détails des utilisateurs.
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
     * Permet de retrouver l'utilisateur sur lequel on travaille à partir d'un query.
     *
     * @param Builder $query
     * @return User|null
     */
    protected function getUserFromQuery(Builder $query)
    {
        $wheres = $query->getQuery()->wheres;

        foreach ($wheres as $where) {
            if ($where['column'] === $this->getTable().'.'.$this->primaryKey[0]	&& $where['operator'] === '=') {
                return User::find($where['value']);
            }
        }

        return null;
    }

    /**
     * Donne l'age de l'utilisateur.
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
     * Indique si l'utilisateur est majeur.
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
                // Si on a pas les données CAS, on regarde avec la date de naissance.
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
     * Donne le login CAS de l'utilisateur.
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
     * Donne le login cotisant BDE de l'utilisateur.
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
     * Indique si l'utilisateur est cotisant BDE.
     *
     * @param  Builder $query
     * @return boolean
     */
    public function isContributorBde(Builder $query)
    {
        $login = $this->loginContributorBde($query);

        if ($login) {
            try {
                return \Ginger::user($login)->isContributor();
            } catch (\Exception $e) {
                // Dans le cas où on est pas contributeur, on renvoie une exception custom.
            }
        }

        throw new PortailException('Non trouvé');
    }
}
