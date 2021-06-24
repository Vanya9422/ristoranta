<?php

namespace App\Repositories\Eloquent\Language;

use App\{Models\Section, Repositories\Eloquent\Repository};

/**
 * Class SectionRepository
 * @package App\Repositories\Eloquent\Language
 */
class SectionRepository extends Repository implements SectionInterface
{
    /**
     * SectionRepository constructor.
     * @param Section $model
     */
    public function __construct(Section $model)
    {
        parent::__construct($model);
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return 'SectionRepository';
    }
}
