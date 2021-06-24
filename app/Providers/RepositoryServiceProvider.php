<?php

namespace App\Providers;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;
use Illuminate\Support\ServiceProvider;

/**
 * Class RepositoryServiceProvider
 * @package App\Providers
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(RepositoryInterface::class, Repository::class);
        $this->registerRepositories();
    }

    /**
     * Register Project Repositories
     */
    public function registerRepositories(): void
    {
        $toBind = config('repositories');

        collect($toBind)->map(function ($repository, $contract) {
            $this->app->bind($contract, $repository);
        });
    }
}
