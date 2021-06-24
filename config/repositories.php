<?php

/** Business Repos and interfaces */

use App\Repositories\Eloquent\Language\{
    LanguageInterface,
    LanguageRepository,
    SectionInterface,
    SectionRepository,
    TranslationInterface,
    TranslationRepository
};

use App\Repositories\Eloquent\Business\{
    BusinessInterface,
    BusinessRepository,
    BusinessTypeInterface,
    BusinessTypeRepository,
    CategoryInterface,
    CategoryRepository,
    DishInterface,
    DishRepository,
    TableInterface,
    TableRepository,
    TagInterface,
    TagRepository
};

use App\Repositories\Eloquent\User\{
    RoleInterface,
    RoleRepository,
    UserInterface,
    UserRepository
};

/** User Repos and interfaces */

/**
 * Project Repositories
 * $key => Contract
 * $value => Repository
 */
return [
    UserInterface::class => UserRepository::class,
    BusinessInterface::class => BusinessRepository::class,
    BusinessTypeInterface::class => BusinessTypeRepository::class,
    TableInterface::class => TableRepository::class,
    RoleInterface::class => RoleRepository::class,
    LanguageInterface::class => LanguageRepository::class,
    TranslationInterface::class => TranslationRepository::class,
    SectionInterface::class => SectionRepository::class,
    DishInterface::class => DishRepository::class,
    CategoryInterface::class => CategoryRepository::class,
    TagInterface::class => TagRepository::class,
];
