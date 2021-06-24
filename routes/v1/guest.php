<?php

/**
 * |--------------------------------------------------------------------------
 * | Application Guest Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register all of the routes for an application.
 * | It is a breeze. Simply tell Lumen the URIs it should respond to
 * | and give it the Closure to call when that URI is requested.
 * |
 */
$router->group([
    'prefix' => VERSION_V1 . '/guest',
    'middleware' => ['bearer_required', 'throttle:3,1', 'hashids', 'assign.guard:guest', 'jwt.auth']
], function () use ($router) {
    $router->post('ask', 'TableActionsController@askWaiter');
    $router->post('bill', 'TableActionsController@billMoney');
    $router->post('other', 'TableActionsController@getOther');
    $router->post('review-table', 'TableActionsController@createReviewTable');
    $router->post('review-business', 'TableActionsController@createReviewBusiness');
});
