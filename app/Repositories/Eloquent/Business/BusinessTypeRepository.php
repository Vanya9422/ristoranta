<?php

namespace App\Repositories\Eloquent\Business;

use App\Models\BusinessType;
use App\Repositories\Eloquent\Repository;

/**
 * Class BusinessTypeRepository
 * @package App\Repositories\Eloquent\Business
 */
class BusinessTypeRepository extends Repository implements BusinessTypeInterface
{
    /**
     * BusinessTypeRepository constructor.
     * @param BusinessType $model
     */
    public function __construct(BusinessType $model)
    {
        parent::__construct($model);
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return 'BusinessTypeRepository';
    }
}
