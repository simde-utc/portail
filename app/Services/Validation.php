<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;

class Validation
{
	protected $request;
	protected $args;

	/**
	 * @param Request $request
	 * @param array $args
	 * @return Validation
	 */
	public function make(Request $request, $args=[]){
		$this->request = $request;
		$this->args = is_array($args) ? $args : [$args];

		return $this;
	}

	/**
	 * @param string $arg
	 * @return Validation
	 */
	public function length($arg){
		$this->args['length'] = $arg;

		return $this;
	}

	/**
	 * @param string $method
	 * @param $args
	 * @return Validation
	 */
	public function __call($method, $args){
		if($this->request->isMethod($method)) {
			foreach ($args as $arg){
				array_push($this->args, $arg);
			}
		}

		return $this;
	}

	/**
	 * @param string $arg
	 * @return Validation $this
	 */
	public function type($arg){
		$this->args['type'] = $arg;

		return $this;
	}

	/**
	 * @param string $table
	 * @param string $fields
	 * @return Validation
	 */
	public function unique($table, $fields){
		$this->args['unique'] = 'unique:'.$table.','.$fields;

		return $this;
	}

	/**
	 * @param string $table
	 * @param string $field
	 * @return Validation
	 */
	public function exists($table, $field){
		array_push($this->args,'exists:'.$table.','.$field);

		return $this;
	}

	/**
	 * @return string
	 */
	public function get(){
		$string = null;
		foreach ($this->args as $key => $arg){
			$string.=$arg.'|';
		}

		return $string;
	}

}
