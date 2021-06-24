<?php

namespace App\Repositories\Eloquent\Business;

/**
 * Interface TagInterface
 * @package App\Repositories\Eloquent\Business
 */
interface TagInterface
{
    /**
     * @param $business_id
     * @param null $tag_id
     * @return mixed
     */
    public function getDishTags($business_id, $tag_id = null);
}
