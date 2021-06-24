<?php

namespace App\Http\Middleware;

use App\Utils\RouteParam;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class WebHookUrlCheck
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $allow
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $allow = '')
    {
        $hash = Hashids::connection('telegram')->encode(ipImplode($request->ip()));
        $token = RouteParam::get($request, 'token');

        if ($allow !== 'all' && !RouteParam::exists($request, 'token') || $token !== $hash) {
            Log::info('WebHookUrlCheck: id - ' . $request->ip());
            return response()->json([
                'code' => 403,
                'message' => 'Fuck You',
            ], 403);
        }

        return $next($request);
    }
}
