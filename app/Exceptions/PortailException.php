<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class PortailException extends HttpException {
	public function __construct(string $message = null, int $statusCode = 400, \Exception $previous = null, array $headers = array(), ?int $code = 0) {
		return parent::__construct($statusCode, $message, $previous, $headers, $code);
	}
}
