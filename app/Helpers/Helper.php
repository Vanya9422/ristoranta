<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
const SUCCESS = 200;
const NOT_FOUND = 404;
const BAD_REQUEST = 400;
const UNAUTHORIZED = 401;
const ERRMESS = 'There was a problem with the system. Please try again later';
const GlobalDateFormat = 'Y-m-d H:i:s';
const StandartDateFormat = 'M d, Y';
const ImagePicsum = 'https://picsum.photos/200';

if (!function_exists('removeImage')) {
    /**
     * @param $path
     * @return bool
     */
    function removeImage($path)
    {
        return file_exists($path) ? unlink($path) : false;
    }
}

if (!function_exists('arraysSum')) {
    /**
     * @param array ...$arrays
     * @return array
     */
    function arraysSum(array ...$arrays)
    {
        return array_map(function (array $array) {
            return array_sum($array);
        }, $arrays);
    }
}

if (!function_exists('ipImplode')) {
    /**
     * @param $ip
     * @return int
     */
    function ipImplode($ip) : int
    {
        $exp = explode('.', $ip);
        return (int)implode($exp);
    }
}

if(!function_exists('public_path'))
{
    /**
     * Return the path to public dir
     * @param null $path
     * @return string
     */
    function public_path($path=null): string
    {
        return rtrim(app()->basePath('public/'.$path), '/');
    }
}

if (!function_exists('urlGenerator')) {
    /**
     * @return \Laravel\Lumen\Routing\UrlGenerator
     */
    function urlGenerator(): \Laravel\Lumen\Routing\UrlGenerator
    {
        return new \Laravel\Lumen\Routing\UrlGenerator(app());
    }
}

if (!function_exists('asset')) {
    /**
     * @param $path
     * @param bool $secured
     *
     * @return string
     */
    function asset($path, $secured = false): string
    {
        return urlGenerator()->asset($path, $secured);
    }
}

if (!function_exists('setUserInData')) {
    /**
     * @param $request
     * @return array
     */
    function setUserInData($request): array
    {
       return array_merge($request->all(), ['user_id' => $request->user()->id]);
    }
}


if (!function_exists('dateCreate')) {
    /**
     * @param $date
     * @return string
     */
    function dateCreate($date)
    {
        return Carbon::parse($date)->format('d-m-Y H:i:s');
    }
}

if (!function_exists('dateHuman')) {
    /**
     * @param $date
     * @return string
     */
    function dateHuman($date)
    {
        return Carbon::createFromTimeStamp(strtotime($date))->diffForHumans();
    }
}

if (!function_exists('getPrimaryImage')) {
    /**
     * @param object $images
     * @return string
     */
    function getPrimaryImage($images)
    {
        return collect($images)->where('is_primary', '=', true)->first();
    }
}
