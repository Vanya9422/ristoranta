<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Unlimited;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class ThrottleRequests
{
    use InteractsWithTime;

    /**
     * The rate limiter instance.
     *
     * @var RateLimiter
     */
    protected RateLimiter $limiter;

    /**
     * Create a new request throttler.
     *
     * @param RateLimiter $limiter
     * @return void
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @param int $maxAttempts
     * @param int $decayMinutes
     * @param string $prefix
     * @return Response
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = ''): Response
    {

        if (app()->environment('local')) {
            return $next($request);
        }

        try {
            if (is_string($maxAttempts)
                && func_num_args() === 3
                && !is_null($limiter = $this->limiter->limiter($maxAttempts))) {
                return $this->handleRequestUsingNamedLimiter($request, $next, $maxAttempts, $limiter);
            }

            return $this->handleRequest(
                $request,
                $next,
                [
                    (object)[
                        'key' => $prefix . $this->resolveRequestSignature($request),
                        'maxAttempts' => $this->resolveMaxAttempts($request, $maxAttempts),
                        'decayMinutes' => $decayMinutes,
                        'responseCallback' => null,
                    ],
                ]
            );
        } catch (ThrottleRequestsException | RuntimeException | HttpResponseException $exception) {
            return \response()->json([
                'code' => 401,
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 401);
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $limiterName
     * @param Closure $limiter
     * @return Response
     *
     * @throws ThrottleRequestsException
     */
    protected function handleRequestUsingNamedLimiter(
        Request $request,
        Closure $next,
        string $limiterName,
        Closure $limiter
    ): Response
    {
        $limiterResponse = call_user_func($limiter, $request);

        if ($limiterResponse instanceof Response) {
            return $limiterResponse;
        } elseif ($limiterResponse instanceof Unlimited) {
            return $next($request);
        }

        return $this->handleRequest(
            $request,
            $next,
            collect(Arr::wrap($limiterResponse))->map(function ($limit) use ($limiterName) {
                return (object)[
                    'key' => md5($limiterName . $limit->key),
                    'maxAttempts' => $limit->maxAttempts,
                    'decayMinutes' => $limit->decayMinutes,
                    'responseCallback' => $limit->responseCallback,
                ];
            })->all()
        );
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param array $limits
     * @return Response
     *
     * @throws ThrottleRequestsException
     */
    protected function handleRequest(Request $request, Closure $next, array $limits): Response
    {
        foreach ($limits as $limit) {
            if ($this->limiter->tooManyAttempts($limit->key, $limit->maxAttempts)) {
                throw $this->buildException($request, $limit->key, $limit->maxAttempts, $limit->responseCallback);
            }

            $this->limiter->hit($limit->key, $limit->decayMinutes * 60);
        }

        $response = $next($request);

        foreach ($limits as $limit) {
            $response = $this->addHeaders(
                $response,
                $limit->maxAttempts,
                $this->calculateRemainingAttempts($limit->key, $limit->maxAttempts)
            );
        }

        return $response;
    }

    /**
     * Resolve the number of attempts if the user is authenticated or not.
     *
     * @param Request $request
     * @param int|string $maxAttempts
     * @return int
     */
    protected function resolveMaxAttempts(Request $request, $maxAttempts): int
    {
        if (Str::contains($maxAttempts, '|')) {
            $maxAttempts = explode('|', $maxAttempts, 2)[$request->user() ? 1 : 0];
        }

        if (!is_numeric($maxAttempts) && $request->user()) {
            $maxAttempts = $request->user()->{$maxAttempts};
        }

        return (int)$maxAttempts;
    }

    /**
     * Resolve request signature.
     *
     * @param Request $request
     * @return string
     *
     * @throws RuntimeException
     */
    protected function resolveRequestSignature(Request $request): string
    {
        if ($user = $request->user()) {
            return sha1($user->getAuthIdentifier());
        } elseif ($request->route()) {
            return sha1(
                $request->method() .
                '|' . $request->server('SERVER_NAME') .
                '|' . $request->path() .
                '|' . $request->ip()
            );
        }

        throw new RuntimeException('Unable to generate the request signature. Route unavailable.');
    }

    /**
     * @param Request $request
     * @param string $key
     * @param int $maxAttempts
     * @param null $responseCallback
     * @return HttpResponseException|ThrottleRequestsException
     */
    protected function buildException(Request $request, string $key, int $maxAttempts, $responseCallback = null)
    {
        $retryAfter = $this->getTimeUntilNextRetry($key);

        $headers = $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );

        return is_callable($responseCallback)
            ? new HttpResponseException($responseCallback($request, $headers))
            : new ThrottleRequestsException('Too Many Attempts.', null, $headers);
    }

    /**
     * Get the number of seconds until the next retry.
     *
     * @param string $key
     * @return int
     */
    protected function getTimeUntilNextRetry(string $key): int
    {
        return $this->limiter->availableIn($key);
    }

    /**
     * Add the limit header information to the given response.
     *
     * @param Response $response
     * @param int $maxAttempts
     * @param int $remainingAttempts
     * @param int|null $retryAfter
     * @return Response
     */
    protected function addHeaders(
        Response $response,
        int $maxAttempts,
        int $remainingAttempts,
        $retryAfter = null
    ): Response
    {
        $response->headers->add(
            $this->getHeaders($maxAttempts, $remainingAttempts, $retryAfter, $response)
        );

        return $response;
    }

    /**
     * Get the limit headers information.
     *
     * @param int $maxAttempts
     * @param int $remainingAttempts
     * @param int|null $retryAfter
     * @param Response|null $response
     * @return array
     */
    protected function getHeaders(
        int $maxAttempts,
        int $remainingAttempts,
        $retryAfter = null,
        ?Response $response = null
    ): array
    {
        if ($response &&
            !is_null($response->headers->get('X-RateLimit-Remaining')) &&
            (int)$response->headers->get('X-RateLimit-Remaining') <= (int)$remainingAttempts) {
            return [];
        }

        $headers = [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ];

        if (!is_null($retryAfter)) {
            $headers['Retry-After'] = $retryAfter;
            $headers['X-RateLimit-Reset'] = $this->availableAt($retryAfter);
        }

        return $headers;
    }

    /**
     * Calculate the number of remaining attempts.
     *
     * @param string $key
     * @param int $maxAttempts
     * @param int|null $retryAfter
     * @return int
     */
    protected function calculateRemainingAttempts(string $key, int $maxAttempts, $retryAfter = null): int
    {
        return is_null($retryAfter) ? $this->limiter->retriesLeft($key, $maxAttempts) : 0;
    }
}
