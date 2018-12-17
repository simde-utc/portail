<?php
/**
 * Service Validation.
 *
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Services;

use Illuminate\Http\Request;

class Validation
{
    protected $args;

    /**
     * Force une longueur maximale.
     *
     * @param string|int $arg
     * @param int     $max
     * @return Validation
     */
    public function length($arg, int $max = null)
    {
        if (is_null($max)) {
            $this->args['length'] = validation_between($arg);
        } else {
            $this->args['length'] = $arg.','.$max;
        }

        return $this;
    }

    /**
     * Spécifie le type.
     *
     * @param string $arg
     * @return Validation $this
     */
    public function type(string $arg)
    {
        $this->args['type'] = $arg;

        return $this;
    }

    /**
     * Indique l'unicité.
     *
     * @param string $table
     * @param string $fields
     * @return Validation
     */
    public function unique(string $table, string $fields)
    {
        $this->args['unique'] = 'unique:'.$table.','.$fields;

        return $this;
    }

    /**
     * Indique la nécessité d'existance.
     *
     * @param string $table
     * @param string $field
     * @return Validation
     */
    public function exists(string $table, string $field)
    {
        array_push($this->args, 'exists:'.$table.','.$field);

        return $this;
    }

    /**
     * Indique que le champ peut être nul.
     *
     * @return Validation
     */
    public function nullable()
    {
        $this->args['nullable'] = 'nullable';

        return $this;
    }

    /**
     * Récupère tous les appels.
     *
     * @param string $method
     * @param array  $args
     * @return Validation
     */
    public function __call(string $method, array $args)
    {
        if ((new Request)->isMethod($method)) {
            foreach ($args as $arg) {
                array_push($this->args, $arg);
            }
        }

        return $this;
    }

    /**
     * Compilation de la validation.
     *
     * @return string
     */
    public function get()
    {
        $result = implode('|', array_values($this->args));
        $this->args = [];

        return $result;
    }
}
