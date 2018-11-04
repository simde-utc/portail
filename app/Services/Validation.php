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

use Symfony\Component\HttpFoundation\Request;

class Validation
{
    protected $request;
    protected $args;

    /**
     * Crée une validation pour une requête.
     *
     * @param Request $request
     * @param array   $args
     * @return Validation
     */
    public function make(Request $request, array $args=[])
    {
        $this->request = $request;
        $this->args = is_array($args) ? $args : [$args];

        return $this;
    }

    /**
     * Force une longueur maximale.
     *
     * @param string $arg
     * @return Validation
     */
    public function length(string $arg)
    {
        $this->args['length'] = $arg;

        return $this;
    }

    /**
     * Récupère tous les appels.
     *
     * @param string $method
     * @param string $args
     * @return Validation
     */
    public function __call(string $method, string $args)
    {
        if ($this->request->isMethod($method)) {
            foreach ($args as $arg) {
                array_push($this->args, $arg);
            }
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
     * Compilation de la validation.
     *
     * @return string
     */
    public function get()
    {
        return implode('|', array_values($this->args));
    }
}
