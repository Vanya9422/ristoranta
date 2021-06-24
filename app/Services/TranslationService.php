<?php

namespace App\Services;

use App\Repositories\Eloquent\Language\LanguageInterface;
use App\Repositories\Eloquent\Language\SectionInterface;
use App\Repositories\Eloquent\Language\TranslationInterface;
use App\Traits\SetRepositories;

/**
 * Class TranslationService
 * @package App\Services
 */
class TranslationService extends CoreService
{
    use SetRepositories;

    /**
     * @var array
     */
    private array $repositories = [];

    /**
     * TranslationService constructor.
     * @param TranslationInterface $translation
     * @param SectionInterface $section
     * @param LanguageInterface $language
     */
    public function __construct(
        TranslationInterface $translation,
        SectionInterface $section,
        LanguageInterface $language
    )
    {
        $this->setRepositories(func_get_args());
    }

    /**
     * @return TranslationInterface
     */
    public function getRepo(): TranslationInterface
    {
        return $this->repositories['TranslationRepository'];
    }

    /**
     * @return SectionInterface
     */
    public function section(): SectionInterface
    {
        return $this->repositories['SectionRepository'];
    }

    /**
     * @return LanguageInterface
     */
    public function language(): LanguageInterface
    {
        return $this->repositories['LanguageRepository'];
    }

    /**
     * @param $section
     * @param array $withRelations
     * @param null $language
     * @return mixed
     */
    public function getTranslations($section, array $withRelations = [], $language = null)
    {
        $conditions = [[
            'relation' => 'section',
            'conditions' => [
                'name' => $section
            ]
        ]];

        if ($language) {
            $conditions[] = [
                'relation' => 'language',
                'conditions' => [
                    'regional' => $language
                ]
            ];
        }

        return $this->getRepo()->findByRelation($conditions, $withRelations, $language);
    }
}
