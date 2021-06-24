<?php

namespace App\Services;

use App\Repositories\Eloquent\Business\BusinessInterface;
use App\Repositories\Eloquent\Business\BusinessTypeInterface;
use App\Repositories\Eloquent\Business\DishInterface;
use App\Repositories\Eloquent\Business\TableInterface;
use App\Repositories\Eloquent\User\UserInterface;
use App\Traits\SetRepositories;

/**
 * Class BusinessService
 * @package App\Services
 */
class BusinessService extends CoreService
{
    use SetRepositories;

    /**
     * @var array
     */
    private array $repositories = [];

    /**
     * UserService constructor.
     *
     * @param BusinessInterface $business
     * @param BusinessTypeInterface $businessType
     * @param TableInterface $table
     * @param UserInterface $worker
     */
    public function __construct(
        BusinessInterface $business,
        BusinessTypeInterface $businessType,
        TableInterface $table,
        UserInterface $worker
    )
    {
        $this->setRepositories(func_get_args());
    }

    /**
     * @return BusinessInterface
     */
    public function getRepo(): BusinessInterface
    {
        return $this->repositories['BusinessRepository'];
    }

    /**
     * @return BusinessTypeInterface
     */
    public function businessType(): BusinessTypeInterface
    {
        return $this->repositories['BusinessTypeRepository'];
    }

    /**
     * @return UserInterface
     */
    public function worker(): UserInterface
    {
        return $this->repositories['UserRepository'];
    }

    /**
     * @return TableInterface
     */
    public function table(): TableInterface
    {
        return $this->repositories['TableRepository'];
    }

    /**
     * @return DishInterface
     */
    public function menu(): DishInterface
    {
        return $this->repositories['DishRepository'];
    }

    /**
     * @param $id
     * @return array
     */
    public function getUserBusinessesAndTypes($id): array
    {
        $businessType = $this->businessType();
        $businessType->setUnsetColumns(['id', 'type']);

        $business = $this->getRepo();
        $business->setUnsetColumns(['id', 'title']);

        return [
            $businessType->all(),
            $business->userGeneralBusinesses($id)
        ];
    }

    /**
     * @param $workerId
     * @param $selected
     * @return mixed
     */
    public function addWorkerToTable($workerId, $selected)
    {
        $worker = $this->worker()->find($workerId);

        $workerData = $worker->hasRole('manager') ? ['manager_id' => $workerId] : ['waiter_id' => $workerId];

        if (!is_array($selected)) {
            $this->table()->update($selected, $workerData);
        } else {
            collect($selected)->each(function ($item) use ($workerData) {
                $this->table()->update($item, $workerData);
            });
        }

        return $this->table()->find($selected, ['waiter', 'manager']);
    }
}
