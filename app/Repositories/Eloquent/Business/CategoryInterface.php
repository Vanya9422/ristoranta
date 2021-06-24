<?php

namespace App\Repositories\Eloquent\Business;

/**
 * Interface CategoryInterface
 * @package App\Repositories\Eloquent\Business
 */
interface CategoryInterface
{
    /**
     * @param $business_id
     * @param null $category_id
     * @return mixed
     */
    public function getBusinessCategories($business_id, $category_id = null);
}
