<?php
/**
 * File generating our exception "Portail" wich allows a structured exception management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class PortailException extends HttpException
{
    /**
     * Exception creation.
     *
     * @param string     $message
     * @param integer    $statusCode
     * @param \Exception $previous
     * @param array      $headers
     * @param integer    $code
     */
    public function __construct(string $message=null, int $statusCode=400,
								\Exception $previous=null, array $headers=[], ?int $code=0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
