<?php

namespace App\Exceptions;

class MemberException extends \Exception {
	public function __construct($message, $code = 400, $previous = null) {
		return parent::__construct($message, $code, $previous);
	}
}
