<?php

/**
 * |--------------------------------------------------------------------------
 * | Application Owner Routes
 * |--------------------------------------------------------------------------
 * | TODO => { add __Slug Models Custom Middleware__ which will give the method a given model... }
 * |---------------------------------------------------------------------------
 * | Here is where you can register all of the routes for an application.
 * | It is a breeze. Simply tell Lumen the URIs it should respond to
 * | and give it the Closure to call when that URI is requested.
 * |
 */
$router->group([
    'prefix' => VERSION_V1 . '/owner',
    'namespace' => 'Owner',
    'middleware' => ['bearer_required', 'jwt.auth', 'role:admin|owner|manager', 'hashids']
], function () use ($router) {

    /**
     *  All Businesses list and auth user business types and user general business,
     *  allowed parameters general and types and relations
     */
    $router->get('businesses', 'BusinessController@getBusinesses');

    $router->group(['prefix' => 'business'], function ($router) {

        /** Business crud **/
        $router->post('', 'BusinessController@create');
        $router->put('', 'BusinessController@update');
        $router->delete('', 'BusinessController@destroy');

        /** Business Details **/
        $router->get('types', 'BusinessController@getBusinessesAndTypes');

        $router->group(['prefix' => '{id}', 'middleware' => 'check_access'], function ($router) {
            $router->get('/', 'BusinessController@show');
            $router->get('workers', 'BusinessController@getWorkers');
            $router->put('workers', 'BusinessController@addWorkers');
            $router->get('tables', 'BusinessController@getTables');
            $router->get('categories', 'CategoryController@index');
            $router->get('tags', 'TagController@index');
            $router->get('dishes', 'MenuController@index');
        });

        $router->group(['prefix' => 'tables'], function ($router) {
            /** Tables crud **/
            $router->post('', 'TableController@create');
            $router->put('', 'TableController@update');
            $router->delete('', 'TableController@destroy');

            /** Table Details **/
            $router->get('{id}', 'TableController@show');
        });

        $router->group(['prefix' => 'workers'], function ($router) {

            /** Workers crud **/
            $router->post('', 'WorkersController@create');
            $router->put('', 'WorkersController@update');
            $router->delete('', 'WorkersController@destroy');

            /** Worker details **/
            $router->get('{id}', 'WorkersController@show');
        });

        $router->group(['prefix' => 'categories'], function ($router) {

            /** Category crud **/
            $router->post('', 'CategoryController@create');
            $router->put('', 'CategoryController@update');
            $router->delete('{id}', 'CategoryController@destroy');
        });

        $router->group(['prefix' => 'tags'], function ($router) {

            /** Tag crud **/
            $router->post('', 'TagController@create');
            $router->put('', 'TagController@update');
            $router->delete('{id}', 'TagController@destroy');
        });

        $router->group(['prefix' => 'menus'], function ($router) {

            /** Menu crud **/
            $router->post('', 'MenuController@create');
            $router->put('', 'MenuController@update');
            $router->delete('{id}', 'MenuController@destroy');
            $router->post('block', 'MenuController@block');
        });
    });
});
