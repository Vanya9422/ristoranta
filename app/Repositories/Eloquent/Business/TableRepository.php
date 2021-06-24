<?php

namespace App\Repositories\Eloquent\Business;

use App\Models\Table;
use App\Repositories\Eloquent\Repository;

class TableRepository extends Repository implements TableInterface
{
    /**
     * TableRepository constructor.
     * @param Table $model
     */
    public function __construct(Table $model)
    {
        parent::__construct($model);
    }

    /**
     * @param $tableId
     * @param $review
     */
    public function createReview($tableId, $review): void
    {
        $this->find($tableId)->waiter()->reviews()->create(['review' => $review]);
    }

    /**
     * @param $tableId
     * @param $data
     */
    public function createBusinessReview($tableId, $data): void
    {
        $this->find($tableId)->business->reviews()->create($data);
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return 'TableRepository';
    }
}
