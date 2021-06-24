<?php

/**
 * |--------------------------------------------------------------------------
 * | Application Admin Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register all of the routes for an application.
 * | It is a breeze. Simply tell Lumen the URIs it should respond to
 * | and give it the Closure to call when that URI is requested.
 * |
 */
$router->group([
    'prefix' => VERSION_V1 . '/admin',
    'namespace' => 'Admin',
    'middleware' => ['jwt.auth', 'role:admin']
], function () use ($router) {

    $router->group(['prefix' => 'languages'], function ($router) {

        /** Language crud **/
        $router->post('', 'LanguageController@create');
        $router->put('', 'LanguageController@update');
        $router->delete('{id}', 'LanguageController@destroy');
    });

    $router->group(['prefix' => 'translations'], function ($router) {

        /** Translation crud **/
        $router->post('', 'TranslationController@create');
        $router->put('', 'TranslationController@update');
        $router->delete('{id}', 'TranslationController@destroy');
    });
});
