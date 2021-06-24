<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfigFiles();
        $this->registerProviders();
    }

    /**
     * Register Project Config Files
     */
    public function registerConfigFiles(): void
    {
        $configs = config('config_files');

        collect($configs)->map(function ($file) {
            $this->app->configure($file);
        });
    }

    /**
     * Register Project Config Files
     */
    public function registerProviders(): void
    {
        $configs = config('providers');

        collect($configs)->map(function ($file) {
            $this->app->register($file);
        });
    }
}
