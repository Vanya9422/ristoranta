<?php

namespace App\Policies;

use App\Models\Table;
use App\Models\User;

/**
 * Class TablePolicy
 * @package App\Policies]
 */
class TablePolicy
{
    /**
     * Determine whether the user can view the post.
     *
     * @param User|null $user
     * @param Table $model
     * @return mixed
     */
    public function create(User $user, Table $model): bool
    {
        $business = $model->business;

        if (($user->id === $business->user_id) || ($business->hasAccess($user->id))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the post.
     *
     * @param User|null $user
     * @param Table $model
     * @return mixed
     */
    public function show(User $user, Table $model): bool
    {
        $business = $model->business;

        if (($user->id === $business->user_id) || ($business->hasAccess($user->id))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param User $user
     * @param Table $model
     * @return bool
     */
    public function update(User $user, Table $model): bool
    {
        $business = $model->business;

        if (($user->id === $business->user_id) || ($business->hasAccess($user->id))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param User $user
     * @param Table $model
     * @return bool
     */
    public function destroy(User $user, Table $model): bool
    {
        $business = $model->business;

        if (($user->id === $business->user_id) || ($business->hasAccess($user->id))) {
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
