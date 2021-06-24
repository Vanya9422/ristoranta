<?php

namespace App\Repositories\Eloquent\Business;

use App\Models\Business;
use App\Repositories\Eloquent\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BusinessRepository
 * @package App\Repositories\Eloquent\Business
 */
class BusinessRepository extends Repository implements BusinessInterface
{
    /**
     * BusinessRepository constructor.
     * @param Business $model
     */
    public function __construct(Business $model)
    {
        parent::__construct($model);
    }

    /**
     * @param int|string $userId
     * @param array $relations
     * @return Builder|Builder[]|Collection|Model|object|null
     */
    public function userGeneralBusinesses($userId, $relations = [])
    {
        return parent::findByCriteria([
            ['user_id', '=', $userId],
            ['parent_id', '=', null]
        ], $relations, true);
    }

    /**
     * @param string $id
     * @param array $relations
     * @return Builder|Builder[]|Collection|Model|object|null
     */
    public function userBusinesses($id, $relations = [])
    {
        return parent::findByCriteria(['user_id' => $id], $relations, true);
    }

    /**
     * @param $id
     * @param $workers
     */
    public function addWorkers($id, $workers): void
    {
        $business = $this->find($id);
        $business->workers()->attach($workers);
    }

    /**
     * @param $id
     * @param $workers
     */
    public function updateWorkers($id, $workers): void
    {
        $business = $this->find($id);
        $business->workers()->sync($workers);
    }

    /**
     * @param $business
     * @return mixed
     */
    public function getWorkers($business)
    {
        return $business->workers()->with('roles')->get(['id', 'first_name', 'last_name']);
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return 'BusinessRepository';
    }
}
