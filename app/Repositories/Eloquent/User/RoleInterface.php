<?php

namespace App\Repositories\Eloquent\User;

/**
 * Interface RoleInterface
 * @package App\Repositories\Eloquent\User
 */
interface RoleInterface
{
    /**
     * @param $id
     * @param object $model
     * @return mixed
     */
    public function assignRoleById($id, object $model);

    /**
     * @param string $name
     * @param object $model
     * @return mixed
     */
    public function assignRoleByName(string $name, object $model);
}
