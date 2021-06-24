<?php

namespace App\Repositories\Eloquent\User;

/**
 * Interface UserInterface
 * @package App\Repositories\Eloquent\User
 */
interface UserInterface
{
    /**
     * @param string $uid
     */
    public function findByUid(string $uid);
}
