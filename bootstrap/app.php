<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('app');
$app->configure('config_files');

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

$app->routeMiddleware([
    'guest' => App\Http\Middleware\Authenticate::class,
    'permission' => Spatie\Permission\Middlewares\PermissionMiddleware::class,
    'role' => Spatie\Permission\Middlewares\RoleMiddleware::class,
    'jwt.auth' => Tymon\JWTAuth\Http\Middleware\Authenticate::class,
    'jwt.refresh' => Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
    'jwt.renew' => Tymon\JWTAuth\Http\Middleware\AuthenticateAndRenew::class,
    'throttle' => App\Http\Middleware\ThrottleRequests::class,
    'bearer_required' => App\Http\Middleware\BearerCheck::class,
    'hashids' => \App\Http\Middleware\RouteHashids::class,
    'assign.guard' => \App\Http\Middleware\AssignGuard::class,
    'hook_check' => \App\Http\Middleware\WebHookUrlCheck::class,
    'check_access' => \App\Http\Middleware\CheckBusinessAccess::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers and Spatie Permissions
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->alias('cache', \Illuminate\Cache\CacheManager::class);

const VERSION_V1 = 'v1';
const CONTROLLERSNAMESPACE = 'App\Http\Controllers\V1';
const ROUTESDIRECTORYV1 = __DIR__ . '/../routes/v1/';

/**
 * |--------------------------------------------------------------------------
 * | Load The Application Routes
 * |--------------------------------------------------------------------------
 * |
 * | Next we will include the routes file so that they can all be added to
 * | the application. This will provide all of the URLs the application
 * | can respond to, as well as the controllers that may handle them.
 *| TODO  {
 *| TODO   "versions" : "add  __Config File__ for versions... ",
 *| TODO  }
 */
$app->router->group(['namespace' => CONTROLLERSNAMESPACE], function ($router) {

    /**
     * Routes For Version 1
     */
    $routesArray = array_reverse(scandir(ROUTESDIRECTORYV1));

    collect($routesArray)->map(function ($fileName) use ($router){
        $path = ROUTESDIRECTORYV1 . $fileName;
        if (is_file($path)) require $path;
    });
});

return $app;
