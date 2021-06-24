<?php

namespace App\Repositories\Eloquent\User;

use App\Models\User;
use App\Repositories\Eloquent\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRepository
 * @package App\Repositories\Eloquent\User
 */
class UserRepository extends Repository implements UserInterface
{
    /**
     * UserRepository constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $uid
     * @return Builder|Model|object|null
     */
    public function findByUid(string $uid)
    {
        return $this->newQuery()->where('uid', $uid)->firstOrFail($this->selects);
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return 'UserRepository';
    }
}
