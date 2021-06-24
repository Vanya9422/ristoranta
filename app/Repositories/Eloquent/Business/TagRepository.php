<?php

namespace App\Repositories\Eloquent\Business;

use App\{Models\Tag, Repositories\Eloquent\Repository};

/**
 * Class TagRepository
 * @package App\Repositories\Eloquent\Business
 */
class TagRepository extends Repository implements TagInterface
{
    /**
     * TagRepository constructor.
     * @param Tag $model
     */
    public function __construct(Tag $model)
    {
        parent::__construct($model);
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return 'TagRepository';
    }

    /**
     * @param $business_id
     * @param null $tag_id
     * @return mixed
     */
    public function getDishTags($business_id, $tag_id = null)
    {
        $conditions = ['business_id' => $business_id];

        if ($tag_id) $conditions = array_merge($conditions, ['id' => $tag_id]);

        return $this->findByCriteria($conditions, [], $tag_id ? 'first' : true);
    }
}
