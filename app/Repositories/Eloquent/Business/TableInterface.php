<?php

namespace App\Repositories\Eloquent\Business;

interface TableInterface
{
    /**
     * @param $tableId
     * @param $review
     */
    public function createReview($tableId, $review): void;

    /**
     * @param $tableId
     * @param $data
     */
    public function createBusinessReview($tableId, $data): void;
}
