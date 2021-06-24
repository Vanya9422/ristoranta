<?php

namespace App\Services;

use App\Repositories\Eloquent\Business\BusinessInterface;
use App\Repositories\Eloquent\User\RoleInterface;
use App\Repositories\Eloquent\User\UserInterface;
use App\Traits\SetRepositories;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserService
 * @package App\Services
 */
class UserService extends CoreService
{
    use SetRepositories;

    /**
     * @var array
     */
    private array $repositories = [];

    /**
     * UserService constructor.
     *
     * @param RoleInterface $roleInterface
     * @param UserInterface $userContract
     * @param BusinessInterface $business
     */
    public function __construct(
        RoleInterface $roleInterface,
        UserInterface $userContract,
        BusinessInterface $business
    ) { $this->setRepositories(func_get_args()); }

    /**
     * @return UserInterface
     */
    public function getRepo(): UserInterface
    {
        return $this->repositories['UserRepository'];
    }

    /**
     * @return RoleInterface
     */
    public function role(): RoleInterface
    {
        return $this->repositories['RoleRepository'];
    }

    /**
     * @return BusinessInterface
     */
    public function business(): BusinessInterface
    {
        return $this->repositories['BusinessRepository'];
    }

    /**
     * @param array $data
     * @return Builder|Model
     */
    public function createRegisterUser(array $data)
    {
        $user = $this->getRepo()->create($data);
        $this->role()->assignRoleByName(config('roles.owner.name'), $user);

        return $user;
    }

    /**
     * @param array $data
     * @return Builder|Model
     */
    public function createOrUpdateBusinessUser(array $data)
    {
        $user = $this->getRepo()->updateOrCreate(
            ['id' => $data['id'] ?? null],
            $data
        );

        if (key_exists('role_id', $data))
            $this->role()->assignRoleById($data['role_id'], $user);

        if (key_exists('business_id', $data)) {
            if (isset($data['id'])) {
                $this->business()->updateWorkers($data['business_id'], $user->id);
            } else {
                $this->business()->addWorkers($data['business_id'], $user->id);
            }
        }

        return $user;
    }
}
