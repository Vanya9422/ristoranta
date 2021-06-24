<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var Auth
     */
    protected Auth $auth;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null): JsonResponse
    {
        if ($this->auth->guard($guard)->guest()) {
            return $this->authorizedErrorResponse();
        }

        return $next($request);
    }

    /**
     * @return JsonResponse
     */
    protected function authorizedErrorResponse(): JsonResponse
    {
        return response()->json([
            'code' => 401,
            'status' => 'error',
            'message' => "401 Authorization required"
        ], 401);
    }
}
