<?php

namespace App\Policies;

use App\Models\Dish;
use App\Models\User;

class MenuPolicy
{

    /**
     * Determine whether the user can view the post.
     *
     * @param User $user
     * @param Dish $menu
     * @return mixed
     */
    public function show(User $user, Dish $menu): bool
    {
        $business = $menu->business;

        if (($user->id === $business->user_id) || ($business->hasAccess($user->id))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the post.
     *
     * @param User $user
     * @param Dish $menu
     * @return mixed
     */
    public function create(User $user, Dish $menu): bool
    {
        $business = $menu->business ?: null;

        if (!$business) return false;

        if (($user->id === $business->user_id) || ($business->hasAccess($user->id))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the post.
     *
     * @param User $user
     * @param Dish $menu
     * @return mixed
     */
    public function update(User $user, Dish $menu): bool
    {
        $business = $menu->business;

        if (!$business) return false;

        if (($user->id === $business->user_id) || ($business->hasAccess($user->id))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the post.
     *
     * @param User $user
     * @param Dish $menu
     * @return mixed
     */
    public function destroy(User $user, Dish $menu): bool
    {
        $business = $menu->business;

        if (!$business) return false;

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
