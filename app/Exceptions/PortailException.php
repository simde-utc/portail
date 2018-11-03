<?php
/**
 * Fichier composant notre exception Portail nous permettant de gérer les exceptions de manière structurée.
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
     * Création de l'exception
     * @param string     $message
     * @param integer    $statusCode
     * @param \Exception $previous
     * @param array      $headers
     * @param integer    $code
     */
    public function __construct(string $message=null, int $statusCode=400,
								\Exception $previous=null, array $headers=[], ?int $code=0)
    {
        return parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
