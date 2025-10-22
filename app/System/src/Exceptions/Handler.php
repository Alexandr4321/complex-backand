<?php

namespace App\System\Exceptions;

use App\System\Responses\JsonResponse;
use App\Laravel\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException as BaseModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException as BaseAuthorizationException;
use Illuminate\Session\TokenMismatchException as BaseTokenMismatchException;
use Illuminate\Validation\ValidationException as BaseValidationException;
use Illuminate\Auth\AuthenticationException as BaseAuthenticationException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * Report or log an exception.
     *
     * @param  Throwable  $exception
     * @return void
     *
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  Throwable  $exception
     * @return Response
     */
    public function render($request, Throwable $exception)
    {
        $exception = $this->prepareException($exception);
    
        return $request->expectsJson()
            ? $this->renderJsonResponse($request, $exception)
            : $this->renderResponse($request, $exception);
    }

    /**
     * Prepare a response for the given exception.
     *
     * @param  Request  $request
     * @param  Throwable  $e
     * @return Response
     */
    protected function renderResponse($request, Throwable $e)
    {
        return parent::prepareResponse($request, $e);
    }

    /**
     * @param  Request  $request
     * @param  BaseException  $e
     * @return JsonResponse
     */
    protected function renderJsonResponse($request, $e)
    {
        return new JsonResponse($e->getDetails(), $e->getMessage(), $e->getCode());
    }

    /**
     * Prepare exception for rendering.
     *
     * @param  Throwable  $e
     * @return BaseException
     */
    protected function prepareException(Throwable $e)
    {
        if ($e instanceof BaseException) {
            return $e;
        }
        
        $data = config('app.debug') ? $this->convertExceptionToArray($e) : [];

        if ($e instanceof BaseAuthenticationException) {
            $e = new AuthenticationException(null, $data, $e);
        } elseif ($e instanceof BaseAuthorizationException) {
            $e = new AuthorizationException(null, $data, $e);
        } elseif ($e instanceof BaseModelNotFoundException) {
            $e = new ModelNotFoundException(null, $data, $e);
        } elseif ($e instanceof NotFoundHttpException) {
            $e = new PageNotFoundException(null, $data, $e);
        } elseif ($e instanceof SuspiciousOperationException) {
            $e = new NotFoundException('Bad hostname provided.', $data, $e);
        } elseif ($e instanceof BaseTokenMismatchException) {
            $e = new TokenMismatchException(null, $data, $e);
        } elseif ($e instanceof BaseValidationException) {
            $e = new ValidationException(null, $e->errors(), $e);
        } else {
            $e = new ServerException($e->getMessage(), $data, $e);
        }

        return $e;
    }
}
