<?php


namespace App\Repositories\Eloquent\Business;

use App\Models\Dish;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface DishInterface
 * @package App\Repositories\Eloquent\Business
 */
interface DishInterface
{
    /**
     * @param $business_id
     * @param null $dish_id
     * @param int|null $perPage
     * @return mixed
     */
    public function getBusinessDishes($business_id, $dish_id = null, ?int $perPage = 20);
}
