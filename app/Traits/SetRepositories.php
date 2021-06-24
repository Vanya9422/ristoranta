<?php

namespace App\Traits;

trait SetRepositories
{
    /**
     * @var string
     */
    private string $method = 'getRepositoryName';

    /**
     * @param array $args
     */
    protected function setRepositories(array $args) : void
    {
        collect($args)->map(function ($repository) {
            if (method_exists ($repository, $this->method)) {
                $this->repositories[$repository->getRepositoryName()] = $repository;
            }
        });
    }
}
