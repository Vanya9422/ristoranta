<?php

use App\Providers\AuthServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\RepositoryServiceProvider;
use Flipbox\LumenGenerator\LumenGeneratorServiceProvider;
use Kreait\Laravel\Firebase\ServiceProvider as KreaitServiceProvider;
use Spatie\Permission\PermissionServiceProvider;
use Telegram\Bot\Laravel\TelegramServiceProvider;
use Tymon\JWTAuth\Providers\LumenServiceProvider;
use Vinkla\Hashids\HashidsServiceProvider;


/**
 * |--------------------------------------------------------------------------
 * | Application Providers
 * |--------------------------------------------------------------------------
 */
return [
    EventServiceProvider::class,
    AuthServiceProvider::class,
    KreaitServiceProvider::class,
    PermissionServiceProvider::class,
    LumenServiceProvider::class,
    RepositoryServiceProvider::class,
    LumenGeneratorServiceProvider::class,
    HashidsServiceProvider::class,
    TelegramServiceProvider::class
];
