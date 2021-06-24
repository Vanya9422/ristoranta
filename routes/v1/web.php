<?php

/** @var Router $router */

use Laravel\Lumen\Routing\Router;

/**
 * |--------------------------------------------------------------------------
 * | Application Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register all of the routes for an application.
 * | It is a breeze. Simply tell Lumen the URIs it should respond to
 * | and give it the Closure to call when that URI is requested.
 * |
 */
$router->group(['prefix' => VERSION_V1], function () use ($router) {

    $router->group(['namespace' => 'Auth'], function () use ($router) {

        $router->post('login', [
            'middleware' => 'throttle:3,1',
            'uses' => 'AuthController@login'
        ]);

        $router->group(
            ['middleware' => ['throttle:3,1', 'bearer_required']],
            function () use ($router) {
                $router->post('register', 'AuthController@register');
                $router->put('reset-password', 'AuthController@passwordReset');
                $router->get('refresh-token', ['middleware' => 'jwt.refresh',
                    function () {
                        return response()->json([
                            'message' => 'Token refreshed!'
                        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
                    }
                ]);
            }
        );

        $router->group(['middleware' => 'throttle:3,1'], function () use ($router) {
            $router->put('password', 'AuthController@passwordMake');
        });

        $router->post('table-auth', [
            'middleware' => ['throttle:3,1', 'hashids', 'assign.guard:guest'],
            'uses' => 'AuthController@tableAuth'
        ]);

        $router->group(
            ['middleware' => ['throttle:3,1', 'bearer_required', 'assign.guard:guest']],
            function () use ($router) {
                $router->get('refresh-token-table', ['middleware' => 'jwt.refresh',
                    function () {
                        return response()->json([
                            'message' => 'Token refreshed!'
                        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
                    }
                ]);
            }
        );

        $router->group(
            ['middleware' => ['bearer_required', 'jwt.auth']],
            function () use ($router) {
                $router->get('logout', 'AuthController@logout');
                $router->get('auth', 'AuthController@authUser');
            }
        );

    });

    $router->group(['middleware' => ['bearer_required', 'jwt.auth']], function () use ($router) {
        $router->get('translations/{section}', 'Admin\TranslationController@show');
        $router->get('languages', 'Admin\LanguageController@index');
    });

    $router->post('webhook/{token}', [
        'middleware' => ['hook_check'],
        'uses' => 'TelegramController@getBotUpdates'
    ]);
});
