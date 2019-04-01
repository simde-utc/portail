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
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Exceptions\MissingScopeException;
use Illuminate\Auth\Access\AuthorizationException;
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

    protected $exceptionToHttpCode = [
        \Laravel\Passport\Exceptions\MissingScopeException::class => 412,
        \Illuminate\Auth\AuthenticationException::class => 401,
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
     * @return mixed
     */
    public function render($request, Exception $exception)
    {
        if ($request->wantsJson() && !($exception instanceof ValidationException)) {
            if ($exception instanceof QueryException) {
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
            } else {
                $status = ($this->exceptionToHttpCode[get_class($exception)] ?? 400);
            }

            if ($exception instanceof MissingScopeException) {
                $scopes = implode(', ', $exception->scopes()[0]);
                $response['message'] = 'Un ou plusieurs scopes sont manquants. Vous devez au moins en avoir un parmi: '.$scopes;
            }

            return response()->json($response, $status);
        } else if ($exception instanceof AuthorizationException) {
            return redirect('/login');
        }

        return parent::render($request, $exception);
    }
}
