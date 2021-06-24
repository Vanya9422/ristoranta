<?php

namespace App\Http\Middleware;

use App\Exceptions\Bearer\BearerRequiredException;
use Closure;
use Illuminate\Http\Request;

class BearerCheck
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {

            if (!$request->bearerToken()) {
                throw new BearerRequiredException('Токен обязательно для авторизации', 400);
            }

            return $next($request);
        } catch (BearerRequiredException $e) {
            return response()->json([
                'code' => $e->getCode(),
                'status' => $e->getStatus(),
                'messages' => $e->getMessage(),
            ], $e->getCode());
        }
    }
}
