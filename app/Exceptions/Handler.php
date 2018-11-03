<?php
/**
 * Fichier générant les exceptions.
 * Gère les exceptions et renvoie un message HTTP
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * Exceptions à ne pas reporter.
     *
     * @var array
     */
    protected $dontReport = [];

    /**
     * Données à ne pas afficher en cas de retour de données formulaires.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Remonte les erreurs ici (via Sentry par ex).
     *
     * @param \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Génère l'erreur gérée.
     *
     * @param  mixed      $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->wantsJson() && !($exception instanceof ValidationException)) {
            if ($exception instanceof \QueryException) {
                $response = [
                    'message' => 'Problème trouvé dans la requête SQL effectuée',
                ];
            } else {
                $response = [
                    'message' => $exception->getMessage(),
                ];
            }

            // Tout montrer en debug.
            if (config('app.debug') && !$this->isHttpException($exception)) {
                $response['message'] = $exception->getMessage();
                $response['exception'] = get_class($exception);
                $response['trace'] = $exception->getTrace();
            }

            if ($this->isHttpException($exception)) {
                $status = $exception->getStatusCode();
            } else if ($exception instanceof AuthenticationException) {
                $status = 401;
            } else {
                $status = 400;
            }

            return response()->json($response, $status);
        } else if ($exception instanceof AuthorizationException) {
            return redirect('/');
        }

        return parent::render($request, $exception);
    }
}
