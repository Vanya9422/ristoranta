<?php

namespace App\Repositories\Eloquent\Language;

/**
 * Interface TranslationInterface
 * @package App\Repositories\Eloquent\Language
 */
interface TranslationInterface
{
    /**
     * @param array $relations
     * @param array $withRelations
     * @param boolean|string $get
     */
    public function findByRelation(array $relations = [], array $withRelations = [], $get = false);
}
