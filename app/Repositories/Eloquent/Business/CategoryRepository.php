<?php

namespace App\Repositories\Eloquent\Business;

use App\{Models\Category, Repositories\Eloquent\Repository};

/**
 * Class CategoryRepository
 * @package App\Repositories\Eloquent\Business
 */
class CategoryRepository extends Repository implements CategoryInterface
{
    /**
     * CategoryRepository constructor.
     * @param Category $model
     */
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return 'CategoryRepository';
    }

    /**
     * @param $business_id
     * @param null $category_id
     * @return mixed
     */
    public function getBusinessCategories($business_id, $category_id = null)
    {
        $conditions = ['business_id' => $business_id];

        if ($category_id) $conditions = array_merge($conditions, ['id' => $category_id]);

        return $this->findByCriteria($conditions, [], $category_id ? 'first' : true);
    }
}
