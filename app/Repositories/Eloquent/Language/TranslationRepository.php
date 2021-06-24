<?php

namespace App\Repositories\Eloquent\Language;

use App\{Models\Translation, Repositories\Eloquent\Repository};

/**
 * Class TranslationRepository
 * @package App\Repositories\Eloquent\Language
 */
class TranslationRepository extends Repository implements TranslationInterface
{
    /**
     * TranslationRepository constructor.
     * @param Translation $model
     */
    public function __construct(Translation $model)
    {
        parent::__construct($model);
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return 'TranslationRepository';
    }

    /**
     * @param array $relations
     * @param array $withRelations
     * @param boolean|string $get
     * @return mixed
     */
    public function findByRelation(array $relations = [], array $withRelations = [], $get = false)
    {
        $query = $this->newQuery();

        collect($relations)->map(function ($relation) use (&$query) {
            $conditions = $relation['conditions'] ?? false;
            $relation = $relation['relation'];

            if ($conditions) {
                $query = $query->whereHas($relation, function ($subCondition) use ($conditions) {
                    $subCondition->where($conditions);
                });
            } else {
                $query = $query->whereHas($relation);
            }
        });

        $query = $query->when(!empty($withRelations), function ($q) use ($withRelations) {
            $q->with($withRelations);
        });

        return is_string($get)
            ? $query->first($this->selects ?: ['*'])
            : $query->get($this->selects ?: ['*']);
    }
}
