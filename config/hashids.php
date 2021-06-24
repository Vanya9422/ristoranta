<?php

/**
 * Copyright (c) Vincent Klaiber.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/vinkla/laravel-hashids
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */
    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */
    'connections' => [

        'main' => [
            'salt' => env('HASHIDS_SALT', 'ksZmtZyv1Vvc7tdp6OH6UTw0sb6BaXZZ'),
            'length' => env('HASHIDS_LENGTH', 20),
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        ],

        'telegram' => [
            'salt' => env('HASHIDS_SALT_TELEGRAM', 'jTcz586LOR2ZFjCFllekItC89GLtzb0G') . env('TELEGRAM_BOT_IP'),
            'length' => env('HASHIDS_LENGTH_TELEGRAM', 20),
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        ],
    ],
];
