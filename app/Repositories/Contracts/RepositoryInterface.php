<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 *|---------------------------------------------------------------------------
 *| Interface RepositoryInterface
 *|---------------------------------------------------------------------------
 *| TODO => { add __Redis Cache System__ in queries... }
 *|---------------------------------------------------------------------------
 *| @package App\Repositories\Contracts
 */
interface RepositoryInterface
{
    /**
     * @return mixed
     */
    public function all();

    /**
     * @param $id
     * @param array $relations
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function find($id, array $relations = []);

    /**
     * @param array $conditions
     * @return mixed
     */
    public function exists(array $conditions = []);

    /**
     * @param array $criteria
     * @param array $relations
     * @param bool|string $get
     * @return mixed
     */
    public function findByCriteria(array $criteria, array $relations = [], $get = false);

    /**
     * @param int $perPage
     * @param array $conditions
     * @return mixed
     */
    public function paginate(int $perPage = 1, array $conditions = []);

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * @param array $conditions
     * @param array $data
     * @return mixed
     */
    public function updateOrCreate(array $conditions, array $data);

    /**
     * @param $id
     * @param array $data
     * @param array $relations
     * @return mixed
     */
    public function updateAndGiveSelf($id, array $data, array $relations = []);

    /**
     * @param array|int $id
     * @return mixed
     */
    public function destroy($id);

    /**
     * @param $model
     * @param bool $forceDelete
     * @return mixed
     */
    public function deleteModel($model, bool $forceDelete = false);

    /**
     * @param $id
     * @param false $forceDelete
     * @return mixed
     */
    public function firstAndDelete($id, bool $forceDelete = false);

    /**
     * @return mixed
     */
    public function newQuery();
}
