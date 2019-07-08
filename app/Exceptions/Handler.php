<?php
/**
 * File generating exceptions.
 * Handles exception and returns a HTTP message. 
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
     * Exceptions not to be reported.
     *
     * @var array
     */
    protected $dontReport = [];

    /**
     * Data not to be displayed in case of form data return.
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
     * Report errors here (via Sentry for example).
     *
     * @param \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Generate handled error.
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

            // Show all in debug.
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
