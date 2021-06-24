<?php

namespace App\Repositories\Eloquent\Business;

use App\Models\Business;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface BusinessInterface
 * @package App\Repositories\Eloquent\Business
 */
interface BusinessInterface
{
    /**
     * @param $userId
     * @param array $relations
     */
    public function userGeneralBusinesses($userId, array $relations = []);

    /**
     * @param $id
     * @param array $relations
     */
    public function userBusinesses($id, array $relations = []);

    /**
     * @param $id
     * @param $workers
     * @return mixed
     */
    public function updateWorkers($id, $workers);

    /**
     * @param $id
     * @param $workers
     * @return mixed
     */
    public function addWorkers($id, $workers);

    /**
     * @param $business
     * @return mixed
     */
    public function getWorkers($business);
}
