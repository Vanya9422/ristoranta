<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

/**
 * Class ProductPolicy
 * @package App\Policies
 */
class BusinessPolicy
{
    /**
     * Determine whether the user can view the post.
     *
     * @param User|null $user
     * @param Business $model
     * @return mixed
     */
    public function hasAccess(User $user, Business $model): bool
    {
        if (($user->id === $model->user_id) || ($model->hasAccess($user->id))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param User $user
     * @param Business $model
     * @return bool
     */
    public function create(User $user, Business $model): bool
    {
        if (($user->id === $model->user_id) || ($model->hasAccess($user->id))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param User $user
     * @param Business $model
     * @return bool
     */
    public function update(User $user, Business $model): bool
    {
        if (($user->id === $model->user_id) || ($model->hasAccess($user->id))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param User $user
     * @param Business $model
     * @return bool
     */
    public function destroy(User $user, Business $model): bool
    {
        if (($user->id === $model->user_id) || ($model->hasAccess($user->id))) {
            return true;
        }

        return false;
    }

    /**
     * Perform pre-authorization checks.
     *
     * @param User $user
     * @return void|bool
     */
    public function after(User $user): bool
    {
        return $user->hasRole(config('roles.admin.name'));
    }
}
