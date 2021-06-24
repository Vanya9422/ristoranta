<?php

namespace App\Repositories\Eloquent\Language;

use App\{Models\Language, Repositories\Eloquent\Repository};

/**
 * Class LanguageRepository
 * @package App\Repositories\Eloquent\Language
 */
class LanguageRepository extends Repository implements LanguageInterface
{
    /**
     * LanguageRepository constructor.
     * @param Language $model
     */
    public function __construct(Language $model)
    {
        parent::__construct($model);
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return 'LanguageRepository';
    }
}
