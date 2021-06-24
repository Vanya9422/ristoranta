<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

trait FuncionAuthorizable
{
    /**
     * @param $function
     * @param Model|Collection $model
     */
    public function getAuthorizeFunction($function, $model): void
    {
        if ($model instanceof Collection) {
            $model->map(function ($item) use ($function) {
                $this->authorize($function, $item);
            });
        }

        if ($model instanceof Model) {
            $this->authorize($function, $model);
        }
    }

    /**
     * @param $function
     * @param array $models
     */
    public function getAuthorizeGateFunction($function, array $models): void
    {
        Gate::authorize($function, $models);
    }
}
