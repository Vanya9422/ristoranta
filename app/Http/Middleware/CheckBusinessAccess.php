<?php

namespace App\Http\Middleware;

use App\Exceptions\Bearer\BearerRequiredException;
use App\Repositories\Eloquent\Business\BusinessInterface;
use App\Traits\Responsable;
use App\Utils\RouteParam;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Class CheckBusinessAccess
 * @property BusinessInterface businessRepo
 * @package App\Http\Middleware
 */
class CheckBusinessAccess
{
    use Responsable;

    /**
     * @var BusinessInterface
     */
    private BusinessInterface $businessRepo;

    /**
     * CheckBusinessAccess constructor.
     * @param BusinessInterface $businessRepo
     */
    public function __construct(BusinessInterface $businessRepo)
    {
        $this->businessRepo = $businessRepo;
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
        $business = $this->businessRepo->find(RouteParam::get($request, 'id'));
        Gate::authorize('hasAccess', $business);
        return $next($request);
    }
}
