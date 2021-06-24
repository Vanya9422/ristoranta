<?php

namespace App\Repositories\Eloquent\Business;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\{Models\Dish, Repositories\Eloquent\Repository};

class DishRepository extends Repository implements DishInterface
{
    /**
     * DishRepository constructor.
     * @param Dish $model
     */
    public function __construct(Dish $model)
    {
        parent::__construct($model);
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return 'DishRepository';
    }

    /**
     * @param $business_id
     * @param null $dish_id
     * @param int|null $perPage
     * @return mixed
     */
    public function getBusinessDishes($business_id, $dish_id = null, ?int $perPage = 20)
    {
        $conditions = ['business_id' => $business_id];

        if ($dish_id) $conditions = array_merge($conditions, ['id' => $dish_id]);

        $model = $this->newQuery()->doesntHave('block')->with(['image', 'tags', 'category'])->where($conditions);

        if ($dish_id) return $model->first($this->selects ?: ['*']);

        return $model->paginate($perPage,$this->selects ?: ['*']);
    }
}
