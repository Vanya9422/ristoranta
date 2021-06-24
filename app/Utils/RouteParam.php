<?php


namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

class RouteParam
{
    /**
     * Get a route parameter from the request.
     *
     * @param Request $request
     * @param string $param
     * @return boolean
     */
    public static function exists(Request $request, string $param): bool
    {
        return Arr::exists($request->route()[2], $param);
    }

    /**
     * Get a route parameter from the request.
     *
     * @param Request $request
     * @return void
     * @throws Throwable
     */
    public function setIfExists(Request &$request): void
    {
        collect($request->route()[2])->map(function ($value, $param) use (&$request) {
            if ($param === 'id' || strpos($param, '_id') !== false) {
                if ($value) {
                    $hashId = Hashids::decode($value);
                    throw_if(empty($hashId), new \Exception('Invalid Hash'));
                    $this->set($request, $param, $hashId[0]);
                }
            }
        });
    }

    /**
     * Get a route parameter from the request.
     *
     * @param Request $request
     * @return void
     */
    public function searchInBody(Request &$request): void
    {
        collect($request->all())->map(function ($value, $param) use (&$request) {

            if ($param === 'id' || strpos($param, '_id') !== false) {
                if ($value) {
                    $hashId = Hashids::decode($value);
                    throw_if(empty($hashId), new \Exception('Invalid Hash'));
                    $request->merge([$param => $hashId[0]]);
                }
            }

            if ($param === 'selected' && !is_array($value)) {
                $hashId = Hashids::decode($value);
                throw_if(empty($hashId), new \Exception('Invalid Hash'));
                $request->merge([$param => $hashId[0]]);
            }

            if ($param === 'selected' && is_array($value)) {
                $decodedSelected = [];
                if (isset($value[0])) {
                    collect($value)->map(function ($val, $key) use (&$decodedSelected) {
                        $hashId = Hashids::decode($val);
                        throw_if(empty($hashId), new \Exception('Invalid Hash'));
                        $decodedSelected['selected'][$key] = $hashId[0];
                    });
                } else {
                    collect($value)->map(function ($val, $key) use (&$decodedSelected) {
                        collect($val)->map(function ($childVal, $childKey) use (&$decodedSelected, $key) {
                            $hashId = Hashids::decode($childVal);
                            throw_if(empty($hashId), new \Exception('Invalid Hash'));
                            $decodedSelected['selected'][$key][$childKey] = $hashId[0];
                        });
                    });
                }

                $request->merge($decodedSelected);
            }
        });
    }

    /**
     * Get a route parameter from the request.
     *
     * @param Request $request
     * @param string $param
     * @param mixed|null $default
     * @return mixed
     */
    public static function get(Request $request, string $param, $default = null)
    {
        return Arr::get($request->route()[2], $param, $default);
    }

    /**
     * Set a route parameter on the request.
     *
     * @param Request $request
     * @param string $param
     * @param mixed $value
     */
    public static function set(Request &$request, string $param, $value)
    {
        $route = $request->route();
        $route[2][$param] = $value;
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });
    }

    /**
     * Forget a route parameter on the Request.
     *
     * @param Request $request
     * @param string $param
     */
    public static function forget(Request &$request, string $param): void
    {
        $route = $request->route();
        Arr::forget($route[2], $param);

        $request->setRouteResolver(function () use ($route) {
            return $route;
        });
    }
}
