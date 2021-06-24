<?php

namespace App\Repositories\Eloquent\User;

use App\{Models\Role, Repositories\Eloquent\Repository};

/**
 * Class RoleRepository
 * @package App\Repositories\Eloquent\User
 */
class RoleRepository extends Repository implements RoleInterface
{
    /**
     * RoleRepository constructor.
     * @param Role $model
     */
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return 'RoleRepository';
    }

    /**
     * @param $id
     * @param object $model
     */
    public function assignRoleById($id, object $model) : void
    {
        $role = $this->find($id);
        $model->syncRoles([$role->name]);
    }

    /**
     * @param string $name
     * @param object $model
     */
    public function assignRoleByName(string $name, object $model): void
    {
        $model->syncRoles([$name]);
    }
}
