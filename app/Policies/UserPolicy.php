<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view the post.
     *
     * @param User|null $user
     * @param User $worker
     * @return mixed
     */
    public function show(User $user, User $worker): bool
    {
        $business = $worker->business[0] ?? null;

        if (!$business) return false;

        if (($user->id === $business->user_id) || ($business->hasAccess($user->id))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param User $user
     * @param User $worker
     * @return bool
     */
    public function create(User $user, User $worker): bool
    {
        $business = $worker->business[0] ?? null;

        if (!$business) return false;

        if (($user->id === $business->user_id) || ($business->hasAccess($user->id))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param User $user
     * @param User $worker
     * @return bool
     */
    public function update(User $user, User $worker): bool
    {
        $business = $worker->business[0] ?? null;

        if (!$business || !$business->existsWorker($worker->id)) return false;

        if (($user->id === $business->user_id) || ($business->hasAccess($user->id))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param User $user
     * @param User $worker
     * @return bool
     */
    public function destroy(User $user, User $worker): bool
    {
        $business = $worker->business[0] ?? null;

        if (!$business || !$business->existsWorker($worker->id)) return false;

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
