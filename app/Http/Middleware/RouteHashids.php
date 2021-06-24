<?php

namespace App\Http\Middleware;

use App\Utils\RouteParam;
use Closure;
use Illuminate\Http\Request;

class RouteHashids
{
    /**
     * @var RouteParam
     */
    protected RouteParam $route;

    /**
     * RouteHashids constructor.
     * @param RouteParam $route
     */
    public function __construct(RouteParam $route)
    {
        $this->route = $route;
    }

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
            /**
             * Decode id if exists get param
             */
            $this->route->setIfExists($request);

            if (!empty($request->all())) {

                /**
                 * Decode id or _id (business_id) if exists request body
                 */
                $this->route->searchInBody($request);
            }

            return $next($request);
        } catch (\Throwable $e) {
            return response()->json(['error' => 400, 'message' => $e->getMessage()], 400);
        }
    }
}
