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
    protected $request;

    /**
     * Defines the request.
     *
     * @param Request $request
     * @return Validation
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Force a max length.
     *
     * @param string|integer $arg
     * @param integer        $max
     * @return Validation
     */
    public function length($arg, int $max=null)
    {
        if (is_null($max)) {
            $this->args['length'] = validation_between($arg);
        } else {
            $this->args['length'] = $arg.','.$max;
        }

        return $this;
    }

    /**
     * Specifies the type.
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
     * Indicates unicity.
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
     * Indicates that the field must exist.
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
     * Indicates that the field can be null.
     *
     * @return Validation
     */
    public function nullable()
    {
        $this->args['nullable'] = 'nullable';

        return $this;
    }

    /**
     * Retrieves all calls.
     *
     * @param string $method
     * @param array  $args
     * @return Validation
     */
    public function __call(string $method, array $args)
    {
        if ($this->request->isMethod($method)) {
            foreach ($args as $arg) {
                array_push($this->args, $arg);
            }
        }

        return $this;
    }

    /**
     * Validation compilation.
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
