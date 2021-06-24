<?php

namespace App\Services;

/**
 * Class CoreService
 * @package App\Services
 */
abstract class CoreService
{
    /**
     * @param array $args
     * @return mixed
     */
    abstract protected function setRepositories(array $args) : void;

    /**
     * @return mixed
     */
    abstract protected function getRepo();
}
