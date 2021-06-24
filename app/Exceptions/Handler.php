<?php

namespace App\Exceptions;

use App\Traits\Responsable;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use Responsable;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $e
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response|JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof NotFoundHttpException | $e instanceof ModelNotFoundException) {
            return $this->notFoundResponse();
        }

        if ($e instanceof UnauthorizedHttpException
            | $e instanceof UnauthorizedException
            | $e instanceof MethodNotAllowedHttpException
        ) {
            if ($e->getStatusCode() === 405) {
                return $this->clientErrorResponse('метод не разрешен', $e->getStatusCode());
            }
            return $this->authorizedErrorResponse($e->getMessage());
        }

        if ($e instanceof Exception | $e instanceof Throwable) {
            return $this->clientErrorResponse(app()->environment('local')
                ? $e->getMessage()
                : ''
            );
        }

        return parent::render($request, $e);
    }
}
